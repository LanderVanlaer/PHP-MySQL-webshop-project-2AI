const bigImage = document.querySelector<HTMLImageElement>('#big-image img');

document.querySelectorAll<HTMLDivElement>('#images div')
  .forEach(div =>
    div.addEventListener('click', () => {
      bigImage.src = div.querySelector<HTMLImageElement>('img').src;
    })
  );
