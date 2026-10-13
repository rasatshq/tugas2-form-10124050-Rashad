document.addEventListener('submit', () => {
  const status = document.querySelector('.loading');
  if (status) status.hidden = false;
});
