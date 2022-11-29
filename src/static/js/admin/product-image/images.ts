enum Type {
  UP = 'up',
  DOWN = 'down',
}

const getImageWrappers = () => document.querySelectorAll<HTMLDivElement>('.product-image-wrapper');

const moveImage = (wrapper: HTMLDivElement, input: HTMLInputElement, type: Type) => {
  const imageWrappers = getImageWrappers();
  const value = Number(input.value);
  let newValue;

  if (type == Type.UP) {
    if (value <= 0) return;
    newValue = value - 1;
    imageWrappers[newValue].before(wrapper);
  } else {
    if (value >= imageWrappers.length - 1) return;
    newValue = value + 1;
    imageWrappers[newValue].after(wrapper);
  }

  input.value = String(newValue);

  imageWrappers[newValue]
    .querySelector<HTMLInputElement>('input.product-image-order-number')
    .value = String(value);
};

getImageWrappers().forEach(wrapper => {
  const up = wrapper.querySelector<HTMLButtonElement>('button.product-image-order-button[data-type="up"]');
  const down = wrapper.querySelector<HTMLButtonElement>('button.product-image-order-button[data-type="down"]');
  const input = wrapper.querySelector<HTMLInputElement>('input.product-image-order-number');

  up.addEventListener('click', () => moveImage(wrapper, input, Type.UP));
  down.addEventListener('click', () => moveImage(wrapper, input, Type.DOWN));
});
