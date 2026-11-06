import { inject, watch } from 'vue';

/**
 * Autosave one editor step: a debounced PUT of the whole form, one request at a time.
 *
 * `afterSave(page)` may copy server values back into the form (e.g. ids of new rows) so the
 * next save updates them instead of creating them again; that change is not saved again.
 */
export function useAutosave(form, url, { afterSave } = {}) {
    const editor = inject('editor');
    let timer = null;
    let pending = false;
    let lastSaved = JSON.stringify(form.data());

    const save = () => {
        if (form.processing) {
            pending = true;
            return;
        }
        if (JSON.stringify(form.data()) === lastSaved) return;

        editor.status.value = 'saving';
        form.put(url, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                afterSave?.(page);
                lastSaved = JSON.stringify(form.data());
                editor.status.value = 'saved';
                editor.saved();
            },
            onError: () => (editor.status.value = 'error'),
            onFinish: () => {
                if (pending) {
                    pending = false;
                    save();
                }
            },
        });
    };

    watch(() => form.data(), () => {
        clearTimeout(timer);
        timer = setTimeout(save, 800);
    }, { deep: true });

    return { save };
}

// Props are reactive proxies, which structuredClone refuses; they only hold plain JSON data anyway.
export const clone = (value) => JSON.parse(JSON.stringify(value));

/**
 * After a list saves, give rows created on this save the ids the server assigned, by position.
 * Key list rows by index, not id: a key that changes when the id arrives would remount the row
 * and drop the focus of someone still typing in it.
 */
export function copyIds(rows, saved) {
    saved.forEach((row, i) => {
        if (rows[i] && !rows[i].id) rows[i].id = row.id;
    });
}

export function moveRow(rows, index, by) {
    const to = index + by;
    if (to < 0 || to >= rows.length) return;
    rows.splice(to, 0, rows.splice(index, 1)[0]);
}
