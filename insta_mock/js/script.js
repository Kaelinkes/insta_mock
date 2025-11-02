// js/script.js
document.addEventListener('DOMContentLoaded', function() {
  // image previews
  function bindPreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('change', function() {
      preview.innerHTML = '';
      const f = input.files[0];
      if (!f) return;
      const img = document.createElement('img');
      img.src = URL.createObjectURL(f);
      img.onload = () => URL.revokeObjectURL(img.src);
      preview.appendChild(img);
    });
  }
  bindPreview('profile-input', 'profile-preview');
  bindPreview('profile-input-2', 'profile-preview-2');
  bindPreview('post-input', 'post-preview');

  // simple live search: shows a link to full results
  const live = document.getElementById('live-search');
  const results = document.getElementById('live-results');
  if (live && results) {
    let t = null;
    live.addEventListener('input', function() {
      const q = live.value.trim();
      if (t) clearTimeout(t);
      if (!q) { results.style.display = 'none'; results.innerHTML = ''; return; }
      t = setTimeout(() => {
        results.style.display = 'block';
        results.innerHTML = '<a href="search.php?q=' + encodeURIComponent(q) + '">View search results for "' + q + '"</a>';
      }, 300);
    });
    document.addEventListener('click', function(evt) {
      if (!results.contains(evt.target) && evt.target !== live) results.style.display = 'none';
    });
  }
});
