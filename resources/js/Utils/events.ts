export interface EventPayload {
    detail: any;
}

class Events<T> {
    private listeners: Record<string, ((payload: T) => void)[]> = {};

    on<P extends T>(event: string, listener: (payload: P) => void) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }

        this.listeners[event].push(listener as (payload: T) => void);
    }

    emit<P extends T>(event: string, payload?: P) {
        this.listeners[event].forEach((listener) => listener(payload));
    }
}

const $events = new Events();
export default $events;
