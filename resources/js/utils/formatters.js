export function formatCurrency(value, currency = 'SAR') {
    if (value === null || value === undefined || value === '') {
        return 'على السوم';
    }

    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency,
        maximumFractionDigits: 0,
    }).format(Number(value));
}

export function formatNumber(value) {
    return new Intl.NumberFormat('ar-SA').format(Number(value || 0));
}

export function formatDate(value) {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('ar-SA', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

export function formatRelativeTime(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    const now = new Date();
    const diffMs = date.getTime() - now.getTime();
    const diffSeconds = Math.round(diffMs / 1000);

    const units = [
        { unit: 'year', seconds: 60 * 60 * 24 * 365 },
        { unit: 'month', seconds: 60 * 60 * 24 * 30 },
        { unit: 'week', seconds: 60 * 60 * 24 * 7 },
        { unit: 'day', seconds: 60 * 60 * 24 },
        { unit: 'hour', seconds: 60 * 60 },
        { unit: 'minute', seconds: 60 },
    ];

    const rtf = new Intl.RelativeTimeFormat('ar', { numeric: 'auto' });

    for (const item of units) {
        const delta = diffSeconds / item.seconds;
        if (Math.abs(delta) >= 1) {
            return rtf.format(Math.round(delta), item.unit);
        }
    }

    return 'الآن';
}
