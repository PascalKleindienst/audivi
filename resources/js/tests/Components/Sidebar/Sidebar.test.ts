import { flushPromises, shallowMount } from '@vue/test-utils';
import Sidebar from '@/Components/Sidebar/Sidebar.vue';
import { expect, vi } from 'vitest';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SidebarNavItem from '@/Components/Sidebar/SidebarNavItem.vue';

describe('Sidebar', () => {
    let wrapper: never;
    const mountOptions = {
        props: {
            showNavigation: false
        }
    };

    beforeEach(() => {
        wrapper = shallowMount(Sidebar, mountOptions);
    });

    // TEARDOWN - run after to each unit test
    afterEach(() => {
        wrapper.unmount();
    });

    it('initializes with correct elements', async () => {
        // Wait until the DOM updates
        await flushPromises();

        expect(wrapper.findAll('[data-testid="sidebar-logo"]')).toHaveLength(1);
        expect(wrapper.findAll('[data-testid="sidebar-logo"]').at(0).findComponent(ApplicationLogo)).toBeTruthy();

        expect(wrapper.findAll('[data-testid="sidebar-nav"]')).toHaveLength(1);
        expect(wrapper.findAll('[data-testid="sidebar-nav"] ul').at(0).findAllComponents(SidebarNavItem)).toHaveLength(
            6
        );

        expect(wrapper.findAll('[data-testid="sidebar-bookshelf"]')).toHaveLength(1);
        expect(wrapper.findAll('[data-testid="sidebar-bookshelf"]').at(0).findComponent(SidebarNavItem)).toBeTruthy();

        expect(wrapper.findAll('[data-testid="sidebar-footer"]')).toHaveLength(1);
        expect(wrapper.findAll('[data-testid="sidebar-footer"]').at(0).findComponent(SidebarNavItem)).toBeTruthy();

        expect(wrapper.find('[data-testid="sidebar-navigation-toggle"]').isVisible()).toBe(false);
    });

    it('processes valid props data', async () => {
        // Update the props passed in to the WeatherResult component
        wrapper.setProps({
            showNavigation: true
        });

        // Wait until the DOM updates
        await flushPromises();

        expect(wrapper.find('[data-testid="sidebar-navigation-toggle"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="sidebar-navigation-toggle"]').isVisible()).toBe(true);
    });

    it('shows to current active navigation item', () => {
        vi.mock('@inertiajs/vue3', async (importOriginal) => ({
            ...(await importOriginal()),
            usePage: () => ({
                component: 'Books'
            })
        }));

        expect(
            wrapper.findAll('[data-testid="sidebar-nav"] ul').at(0).findAllComponents(SidebarNavItem).at(1).props()
                .active
        ).toBe(true);
    });

    it('emits a custom event when the close navigation button is clicked', () => {
        wrapper.findAll('[data-testid="sidebar-navigation-toggle"]').at(0).trigger('click');
        expect(wrapper.emitted('closeNavigation')).toBeTruthy();
        expect(wrapper.emitted('closeNavigation').length).toBe(1);
    });
});
