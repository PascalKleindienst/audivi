type Direction = 'vertical' | 'horizontal';
export interface ArrowNavigationOptions {
    /**
     * The attribute name to find the collection items in the parent element.
     *
     * @defaultValue "data-radix-vue-collection-item"
     */
    attributeName?: string;

    /**
     * The parent element where contains all the collection items, this will collect every item to be used when nav
     * It will be ignored if attributeName is provided
     *
     * @defaultValue []
     */
    itemsArray?: HTMLElement[];

    /**
     * Allow loop navigation. If false, it will stop at the first and last element
     *
     * @defaultValue true
     */
    loop?: boolean;

    /**
     * Prevent the scroll when navigating. This happens when the direction of the
     * key matches the scroll direction of any ancestor scrollable elements.
     *
     * @defaultValue true
     */
    preventScroll?: boolean;

    /**
     * Focus the element after navigation
     *
     * @defaultValue false
     */
    focus?: boolean;
}

/**
 * Recursive function to find the next focusable element to avoid disabled elements
 */
function findNextFocusableElement(
    elements: HTMLElement[],
    currentElement: HTMLElement,
    direction: Direction,
    forward: boolean,
    loop: boolean,
    iterations = elements.length
): HTMLElement | null {
    if (--iterations === 0) return null;

    const index = elements.indexOf(currentElement);

    const getItemsPerRow = (): number => {
        const baseOffset = elements[0].offsetTop;
        const breakIndex = elements.findIndex((item) => item.offsetTop > baseOffset);
        return breakIndex === -1 ? elements.length : breakIndex;
    };

    let newIndex = forward ? index + 1 : index - 1;

    if (direction === 'vertical') {
        newIndex = forward ? index + getItemsPerRow() : index - getItemsPerRow();
    }

    if (!loop && (newIndex < 0 || newIndex >= elements.length)) return null;

    const adjustedNewIndex = (newIndex + elements.length) % elements.length;
    const candidate = elements[adjustedNewIndex];

    if (!candidate) return null;

    const isDisabled = candidate.hasAttribute('disabled') && candidate.getAttribute('disabled') !== 'false';

    if (isDisabled) {
        return findNextFocusableElement(elements, candidate, direction, forward, loop, iterations);
    }

    return candidate;
}

export function useGridNavigation(
    e: KeyboardEvent,
    currentElement: HTMLElement,
    parentElement: HTMLElement | undefined,
    options: ArrowNavigationOptions = {}
): HTMLElement | null {
    if (!currentElement) {
        return null;
    }

    const {
        loop = true,
        attributeName = '[data-grid-item]',
        itemsArray = [],
        preventScroll = true,
        focus = true
    } = options;

    const [right, left, up, down, home, end] = [
        e.key === 'ArrowRight',
        e.key === 'ArrowLeft',
        e.key === 'ArrowUp',
        e.key === 'ArrowDown',
        e.key === 'Home',
        e.key === 'End'
    ];

    const goingVertical = up || down;
    const goingHorizontal = right || left;

    const allCollectionItems: HTMLElement[] = parentElement
        ? Array.from(parentElement.querySelectorAll(attributeName))
        : itemsArray;

    if (!allCollectionItems.length) return null;

    if (preventScroll) e.preventDefault();

    let item: HTMLElement | null = null;

    if (goingHorizontal || goingVertical) {
        item = findNextFocusableElement(
            allCollectionItems,
            currentElement,
            goingVertical ? 'vertical' : 'horizontal',
            goingVertical ? down : right,
            loop
        );
    } else if (home) {
        item = allCollectionItems.at(0) || null;
    } else if (end) {
        item = allCollectionItems.at(-1) || null;
    }

    if (focus) {
        item?.focus();
    }

    return item;
}
