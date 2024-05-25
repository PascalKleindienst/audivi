import { useHumanFileSize } from '@/Composables/file';

describe('useHumanFileSize', () => {
    it('should format bytes as human-readable text', () => {
        expect(useHumanFileSize(0)).toBe('0 B');
        expect(useHumanFileSize(1024)).toBe('1.0 KiB');
        expect(useHumanFileSize(1024 * 1024)).toBe('1.0 MiB');
        expect(useHumanFileSize(1024 * 1024 * 1024)).toBe('1.0 GiB');
        expect(useHumanFileSize(1024 * 1024 * 1024 * 1024)).toBe('1024.0 GiB');
    });

    it('should use binary units by default', () => {
        expect(useHumanFileSize(1024)).toBe('1.0 KiB');
    });

    it('should use metric units when si=true', () => {
        expect(useHumanFileSize(1000, true)).toBe('1.0 kB');
    });

    it('should display the specified number of decimal places', () => {
        expect(useHumanFileSize(1024, false, 2)).toBe('1.00 KiB');
        expect(useHumanFileSize(1024 * 1024, false, 2)).toBe('1.00 MiB');
    });
});
