// Simple factory to create a TPB store modal (placeholder API)
export type StoreModalOptions = { title?: string };

export function createStoreModal(options: StoreModalOptions = {}) {
  const title = options.title ?? 'TPB Store Modal';
  return {
    open() {
      return `${title} opened`;
    },
    close() {
      return `${title} closed`;
    },
  };
}


