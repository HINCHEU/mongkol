# Mongkol — server commands
#
#   make install    first deploy on a new server
#   make deploy     every deploy after that
#   make doctor     check the server is configured the way you think
#
# Local development runs on XAMPP, so every target here is for the server.
# Every target is safe to re-run. Destructive ones ask first.

SHELL := /bin/bash
DC    := docker compose -f compose.yaml$(shell grep -qE '^PROXY_NETWORK=.+' .env 2>/dev/null && echo ' -f compose.proxy.yaml')
ART   := $(DC) exec -T app php artisan

.DEFAULT_GOAL := help
.PHONY: help install deploy migrate doctor login-link backup restore logs shell tinker down up

help:
	@echo "Mongkol"
	@echo
	@echo "  install        First deploy: .env checks, key, build, migrate"
	@echo "  deploy         Back up, pull, build, migrate"
	@echo "  doctor         Check the deployment — run after every deploy"
	@echo "  migrate        Run pending migrations"
	@echo "  login-link     Sign-in link without Google (make login-link email=you@example.com)"
	@echo "  backup         Dump the database and storage to backups/"
	@echo "  restore        Restore a database dump (make restore f=backups/db-....sql.gz)"
	@echo "  logs           Follow logs   (make logs s=app)"
	@echo "  shell          Shell in the app container"
	@echo "  tinker         Laravel tinker in the app container"
	@echo "  down / up      Maintenance mode on / off"

# --------------------------------------------------------------- setup ---

install:
	@if [ ! -f .env ]; then \
		cp .env.production.example .env; \
		echo "==> Created .env from .env.production.example. Set APP_URL, PROXY_NETWORK,"; \
		echo "    DB_PASSWORD, DB_ROOT_PASSWORD, GOOGLE_* and ADMIN_EMAILS, then run 'make install' again."; \
		exit 1; \
	fi
	@command -v docker >/dev/null 2>&1 || { \
		echo "!!! docker is not installed."; \
		echo "      curl -fsSL https://get.docker.com | sudo sh"; \
		exit 1; \
	}
	@if grep -qE '^APP_ENV=local' .env; then \
		echo "!!! APP_ENV=local in .env. Set production or staging first."; exit 1; \
	fi
	@if grep -qE '^DB_(ROOT_)?PASSWORD=(change-me.*)?$$' .env; then \
		echo "!!! DB_PASSWORD or DB_ROOT_PASSWORD is empty or still the example value."; exit 1; \
	fi
	@net=$$(grep -E '^PROXY_NETWORK=' .env | cut -d= -f2); \
	if [ -n "$$net" ] && ! docker network inspect "$$net" >/dev/null 2>&1; then \
		echo "!!! PROXY_NETWORK=$$net does not exist. Nginx Proxy Manager's networks:"; \
		docker network ls --format '      {{.Name}}' | grep -v -E '^\s+(bridge|host|none)$$'; \
		exit 1; \
	fi
	@# The image has no .env to write to, so key:generate cannot run inside it.
	@if grep -qE '^APP_KEY=\s*$$' .env; then \
		key="base64:$$(openssl rand -base64 32)"; \
		sed -i "s|^APP_KEY=.*|APP_KEY=$$key|" .env; \
		echo "==> Generated APP_KEY."; \
	fi
	$(DC) build
	$(DC) up -d --wait --remove-orphans
	$(ART) migrate --force
	@echo
	@echo "==> Installed at $$(grep ^APP_URL .env | cut -d= -f2)"
	@echo "      Sign in with Google using an ADMIN_EMAILS address, then open /admin."
	@echo "      make doctor   to check it is configured the way you think"

# -------------------------------------------------------------- deploy ---

deploy: backup
	@echo "==> Deploying..."
	git pull --ff-only
	$(DC) build
	$(DC) up -d --wait --remove-orphans
	@# Down after `up`: the maintenance flag lives in the container, so setting
	@# it on the old one does not survive the recreate. The secret lets whoever
	@# is deploying keep browsing to check the result.
	$(ART) down --secret="deploy-check"
	$(ART) migrate --force
	$(ART) up
	@echo "==> Deployed."
	@$(MAKE) --no-print-directory doctor

migrate:
	$(ART) migrate --force

login-link:
	@[ -n "$(email)" ] || { echo "Usage: make login-link email=you@example.com"; exit 1; }
	@$(ART) user:login-link "$(email)"

# -------------------------------------------------------------- doctor ---
#
# One shell invocation held together by line continuations: a `#` comment
# inside it ends the command, so notes inside use `:` instead.

doctor:
	@source .env 2>/dev/null; \
	fail=0; \
	chk() { if [ "$$2" = "ok" ]; then printf "  \033[32m✓\033[0m %s\n" "$$1"; \
	        else printf "  \033[31m✗\033[0m %s — %s\n" "$$1" "$$2"; fail=1; fi; }; \
	echo "==> Checking the deployment (APP_ENV=$$APP_ENV)"; \
	\
	case "$$APP_ENV" in production|staging) chk "APP_ENV is production or staging" ok ;; \
	  *) chk "APP_ENV is production or staging" "it is '$$APP_ENV' — config and routes are not cached" ;; esac; \
	\
	if [ "$$($(ART) tinker --execute='echo config("app.debug") ? "on" : "off";' 2>/dev/null | tr -d '\r\n')" = "off" ]; \
	  then chk "APP_DEBUG is off" ok; else chk "APP_DEBUG is off" "it is ON — stack traces are public"; fi; \
	\
	if [ -n "$$APP_KEY" ]; then chk "APP_KEY is set" ok; else chk "APP_KEY is set" "empty — every request fails"; fi; \
	\
	case "$$APP_URL" in https://*) chk "APP_URL is https" ok ;; \
	  *) chk "APP_URL is https" "it is '$$APP_URL' — Google refuses non-https redirect URIs" ;; esac; \
	\
	if [ -n "$$GOOGLE_CLIENT_ID" ] && [ -n "$$GOOGLE_CLIENT_SECRET" ]; \
	  then chk "Google OAuth is configured" ok; \
	  else chk "Google OAuth is configured" "GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET is empty — nobody can sign in"; fi; \
	\
	if [ -n "$$ADMIN_EMAILS" ]; then chk "ADMIN_EMAILS is set" ok; \
	  else chk "ADMIN_EMAILS is set" "empty — nobody can open /admin"; fi; \
	\
	: "Not 'compose port': newer Compose prints ':0' for an unpublished port."; \
	if $(DC) ps mysql 2>/dev/null | grep -q -- '->'; \
	  then chk "MySQL is not published" "a host port maps to it"; \
	  else chk "MySQL is not published" ok; fi; \
	\
	migrations=$$($(ART) migrate:status 2>/dev/null | tr -d '\r'); \
	if echo "$$migrations" | grep -q 'Pending'; \
	  then chk "Migrations are applied" "$$(echo "$$migrations" | grep -c 'Pending') pending — run: make migrate"; \
	elif echo "$$migrations" | grep -q 'Ran'; \
	  then chk "Migrations are applied" ok; \
	  else chk "Migrations are applied" "migrate:status returned nothing — is the database reachable?"; fi; \
	\
	if $(DC) exec -T nginx wget -q -O /dev/null http://127.0.0.1/up 2>/dev/null; \
	  then chk "nginx reaches PHP" ok; \
	  else chk "nginx reaches PHP" "/up inside the nginx container failed — see: make logs s=app"; fi; \
	\
	code=$$(curl -s -o /dev/null -m 10 -w '%{http_code}' "$$APP_URL/up" 2>/dev/null); \
	case "$$code" in \
	  200) chk "$$APP_URL answers" ok ;; \
	  000) chk "$$APP_URL answers" "no answer — DNS record, or the proxy host for it?" ;; \
	  *) chk "$$APP_URL answers" "/up returned $$code — is the proxy forwarding to http://mongkol-web:80?" ;; \
	esac; \
	\
	echo; \
	if [ $$fail -eq 0 ]; then echo "  All checks passed."; \
	  else echo "  Some checks failed. Each line above says what it costs."; exit 1; fi

# ---------------------------------------------------------------- data ---

backup:
	@mkdir -p backups
	@ts=$$(date +%Y%m%d-%H%M%S); \
	set -o pipefail; \
	$(DC) exec -T mysql sh -c 'exec mysqldump -uroot -p"$$MYSQL_ROOT_PASSWORD" --single-transaction --routines "$$MYSQL_DATABASE"' \
		| gzip > backups/db-$$ts.sql.gz \
		&& $(DC) exec -T app tar czf - -C storage/app . > backups/storage-$$ts.tgz \
		&& echo "==> Backed up to backups/db-$$ts.sql.gz and backups/storage-$$ts.tgz" \
		|| { echo "!!! Backup failed."; rm -f backups/db-$$ts.sql.gz backups/storage-$$ts.tgz; exit 1; }

restore:
	@[ -n "$(f)" ] && [ -f "$(f)" ] || { echo "Usage: make restore f=backups/db-YYYYMMDD-HHMMSS.sql.gz"; ls -1t backups/db-*.sql.gz 2>/dev/null | head -5; exit 1; }
	@echo "!!! This replaces the database with $(f)."
	@read -p "    Type the database name to confirm: " ans; \
	if [ "$$ans" != "$$(grep ^DB_DATABASE .env | cut -d= -f2)" ]; then echo "==> Aborted."; exit 1; fi; \
	set -o pipefail; \
	gunzip -c "$(f)" | $(DC) exec -T mysql sh -c 'exec mysql -uroot -p"$$MYSQL_ROOT_PASSWORD" "$$MYSQL_DATABASE"' \
		&& echo "==> Restored. Run 'make migrate' if the dump is older than the code."

# ---------------------------------------------------------- day to day ---

logs:
	$(DC) logs -f --tail=200 $(s)

shell:
	$(DC) exec app sh

tinker:
	$(DC) exec app php artisan tinker

down:
	$(ART) down

up:
	$(ART) up
