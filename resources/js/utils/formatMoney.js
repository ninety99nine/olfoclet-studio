/**
 * Format a money value for display using BWP currency symbol "P".
 * Used everywhere amounts are shown on the frontend.
 *
 * @param {number|string|{ amount?: number, currency?: string, amount_with_currency?: string }|null|undefined} value
 * @returns {string} e.g. "P1.00" or "—"
 */
export function formatMoney(value) {
    if (value == null) return '—';

    let num = null;

    if (typeof value === 'number' && !Number.isNaN(value)) {
        num = value;
    } else if (typeof value === 'object') {
        if (value.amount != null && !Number.isNaN(Number(value.amount))) {
            num = Number(value.amount);
        } else if (typeof value.amount_with_currency === 'string') {
            return formatMoneyFromString(value.amount_with_currency);
        }
    } else if (typeof value === 'string') {
        return formatMoneyFromString(value);
    }

    if (num !== null) {
        const formatted = Number(num).toFixed(2);
        return `P${formatted}`;
    }

    return '—';
}

/**
 * Normalize a string that may be "1.00", "P1.00", "1.00 BWP", etc. to "P1.00".
 * @param {string} str
 * @returns {string}
 */
function formatMoneyFromString(str) {
    if (!str || typeof str !== 'string') return '—';
    const trimmed = str.trim();
    if (!trimmed) return '—';
    // Replace BWP or P prefix/suffix and extract number
    const normalized = trimmed.replace(/\s*BWP\s*/gi, '').replace(/^P\s*/i, '').replace(/\s*P\s*$/i, '');
    const num = parseFloat(normalized);
    if (!Number.isNaN(num)) {
        return `P${num.toFixed(2)}`;
    }
    // Already has P, or return as-is if we can't parse
    if (/P/i.test(trimmed)) return trimmed;
    return trimmed;
}
