const like = document.getElementById('like');
like.addEventListener('click', () => {
  const formdata = new FormData();
  formdata.append('product-id', location.pathname.match(/^\/product\/(\d+)/)[1]);

  fetch('/api/like', {
    method: 'POST',
    body: formdata,
    redirect: 'follow'
  })
    .then(response => response.json())
    .then((result: { 'product-id': number, like: boolean }) => like.classList.toggle('liked', result.like))
    .catch(error => console.log('error', error));
});
