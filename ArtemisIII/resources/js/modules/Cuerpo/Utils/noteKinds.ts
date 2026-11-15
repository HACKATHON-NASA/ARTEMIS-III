import { t } from '@/lib/i18n';
import type { NoteKind } from '../Types/notes';

/** El nombre y la pista de cada tipo de nota clínica (en el idioma de ahora). */
type NoteKindInfo = { readonly label: string; readonly hint: string };

function kind(code: NoteKind): NoteKindInfo {
    return {
        get label() {
            return t(`Cuerpo.noteKinds.${code}.label`);
        },
        get hint() {
            return t(`Cuerpo.noteKinds.${code}.hint`);
        },
    };
}

export const NOTE_KINDS: Record<NoteKind, NoteKindInfo> = {
    evolution: kind('evolution'),
    impression: kind('impression'),
    plan: kind('plan'),
    follow_up: kind('follow_up'),
};
