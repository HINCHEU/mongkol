<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({ themes: Array });

const form = useForm({
    theme_id: props.themes[0]?.id ?? null,
    wedding_date: '',
    groom: { name_km: '', name_en: '' },
    bride: { name_km: '', name_en: '' },
});
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Breadcrumb & Title -->
            <div class="mb-6">
                <Link href="/app" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center gap-1.5 mb-2 transition-colors">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ $t('common.back_to_invitations') }}
                </Link>
                <h1 class="text-2xl font-bold tracking-tight text-stone-900 sm:text-3xl">{{ $t('create.title') }}</h1>
                <p class="mt-1 text-sm text-stone-700 font-kantumruy">{{ $t('create.subtitle') }}</p>
            </div>

            <form class="grid gap-6" @submit.prevent="form.post('/app/invitations')">
                <!-- Theme Selection Card -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-semibold text-stone-900">{{ $t('create.theme_title') }}</h2>
                            <p class="text-xs text-stone-600 font-kantumruy">{{ $t('create.theme_subtitle') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <label
                            v-for="theme in themes"
                            :key="theme.id"
                            class="flex items-center justify-between gap-4 rounded-lg border p-4 transition-all duration-150 cursor-pointer"
                            :class="form.theme_id === theme.id ? 'border-amber-600/80 bg-amber-50/40 shadow-xs ring-1 ring-amber-500/20' : 'border-stone-200 bg-white hover:border-stone-300'"
                        >
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="form.theme_id"
                                    type="radio"
                                    :value="theme.id"
                                    class="size-4 text-[#7B1122] accent-[#7B1122] focus:ring-amber-500"
                                />
                                <div>
                                    <span class="font-semibold text-stone-900">{{ theme.name }}</span>
                                    <span v-if="theme.tier === 'premium'" class="ml-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-900">{{ $t('common.premium') }}</span>
                                </div>
                            </div>
                            <a :href="`/themes/${theme.slug}`" target="_blank" class="text-xs font-semibold text-amber-800 hover:text-amber-900 hover:underline">
                                {{ $t('common.view_demo') }} ↗
                            </a>
                        </label>
                    </div>
                    <p v-if="form.errors.theme_id" class="mt-2 text-xs font-medium text-red-700">{{ form.errors.theme_id }}</p>
                </div>

                <!-- Couple Information Cards -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <!-- Groom -->
                    <div class="card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-800 text-xs font-bold">1</div>
                            <h2 class="text-base font-semibold text-stone-900">{{ $t('common.groom') }}</h2>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-stone-700 mb-1">{{ $t('create.name_km') }}</label>
                                <input
                                    v-model="form.groom.name_km"
                                    lang="km"
                                    :placeholder="$t('create.example', { name: 'ចាន់ សុខា' })"
                                    class="input font-kantumruy"
                                    required
                                />
                                <span v-if="form.errors['groom.name_km']" class="mt-1 block text-xs text-red-700">{{ form.errors['groom.name_km'] }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-stone-700 mb-1">{{ $t('create.name_latin') }}</label>
                                <input
                                    v-model="form.groom.name_en"
                                    :placeholder="$t('create.example', { name: 'Chan Sokha' })"
                                    class="input"
                                    required
                                />
                                <span v-if="form.errors['groom.name_en']" class="mt-1 block text-xs text-red-700">{{ form.errors['groom.name_en'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bride -->
                    <div class="card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-800 text-xs font-bold">2</div>
                            <h2 class="text-base font-semibold text-stone-900">{{ $t('common.bride') }}</h2>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-stone-700 mb-1">{{ $t('create.name_km') }}</label>
                                <input
                                    v-model="form.bride.name_km"
                                    lang="km"
                                    :placeholder="$t('create.example', { name: 'លី ដារ៉ា' })"
                                    class="input font-kantumruy"
                                    required
                                />
                                <span v-if="form.errors['bride.name_km']" class="mt-1 block text-xs text-red-700">{{ form.errors['bride.name_km'] }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-stone-700 mb-1">{{ $t('create.name_latin') }}</label>
                                <input
                                    v-model="form.bride.name_en"
                                    :placeholder="$t('create.example', { name: 'Ly Dara' })"
                                    class="input"
                                    required
                                />
                                <span v-if="form.errors['bride.name_en']" class="mt-1 block text-xs text-red-700">{{ form.errors['bride.name_en'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wedding Date Card -->
                <div class="card p-6">
                    <h2 class="text-base font-semibold text-stone-900 mb-1">{{ $t('create.wedding_date') }}</h2>
                    <p class="text-xs text-stone-600 mb-3 font-kantumruy">{{ $t('create.wedding_date_hint') }}</p>

                    <div>
                        <input
                            v-model="form.wedding_date"
                            type="date"
                            class="input max-w-sm"
                            required
                        />
                        <span v-if="form.errors.wedding_date" class="mt-1.5 block text-xs font-medium text-red-700">{{ form.errors.wedding_date }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between gap-4 pt-2">
                    <Link href="/app" class="btn-secondary">
                        {{ $t('common.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        class="btn-primary"
                        :disabled="form.processing"
                    >
                        <span>{{ $t('create.submit') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>