import { getActiveLanguage } from 'laravel-vue-i18n';

/**
 * Format bytes as human-readable text.
 *
 * @param bytes Number of bytes.
 * @param dp Number of decimal places to display.
 *
 * @return Formatted string.
 */
export const useHumanFileSize = (bytes: number, dp = 1) => {
    if (Math.abs(bytes) < 1024) {
        return bytes + ' B';
    }

    const units = ['KB', 'MB', 'GB'];
    let u = -1;
    const r = 10 ** dp;

    do {
        bytes /= 1024;
        ++u;
    } while (Math.round(Math.abs(bytes) * r) / r >= 1024 && u < units.length - 1);

    return bytes.toFixed(dp) + ' ' + units[u];
};

export const useHumanTime = (seconds: number, format: 'short' | 'long' = 'short') => {
    const levels = {
        hours: Math.floor(seconds / 3600),
        minutes: Math.floor((seconds % 3600) / 60),
        seconds: Math.floor(seconds % 60)
    };

    // Format as HH:MM:SS
    const formattedHours = String(levels.hours).padStart(2, '0');
    const formattedMinutes = String(levels.minutes).padStart(2, '0');
    const formattedSeconds = String(levels.seconds).padStart(2, '0');

    if (format === 'short') {
        if (levels.hours === 0) {
            return `${formattedMinutes}:${formattedSeconds}`;
        }

        return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
    }

    if (levels.hours === 0) {
        return `${formattedMinutes}min ${formattedSeconds}s`;
    }

    return `${formattedHours}h ${formattedMinutes}min ${formattedSeconds}s`;
};

export const useHumanDate = (date: Date) => {
    return new Intl.DateTimeFormat(getActiveLanguage(), {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
};
