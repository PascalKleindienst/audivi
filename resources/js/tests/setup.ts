import { config } from '@vue/test-utils';

import { vi } from 'vitest';

vi.mock('ziggy-js', () => ({
    route: (name) => name
}));
