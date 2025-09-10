// Basic unit tests for the modal factory
import { describe, it, expect } from 'vitest';
import { createStoreModal } from '../src';
describe('createStoreModal', () => {
    it('opens and closes with default title', () => {
        const modal = createStoreModal();
        expect(modal.open()).toBe('TPB Store Modal opened');
        expect(modal.close()).toBe('TPB Store Modal closed');
    });
    it('respects custom title', () => {
        const modal = createStoreModal({ title: 'Custom' });
        expect(modal.open()).toBe('Custom opened');
        expect(modal.close()).toBe('Custom closed');
    });
});
