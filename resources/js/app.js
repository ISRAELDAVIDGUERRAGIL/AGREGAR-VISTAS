
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function csrf() {
  const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
  return m ? decodeURIComponent(m[1]) : '';
}

async function apiRequest(method, url, body) {
  const h = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
  if (body) h['X-XSRF-TOKEN'] = csrf();
  const r = await fetch('/api' + url, { method, headers: h, body: body ? JSON.stringify(body) : null });
  if (!r.ok) throw await r.json().catch(() => ({}));
  return r.json();
}

window.api = {
  listar:     (ruta, params = '') => apiRequest('GET',    `/${ruta}${params}`),
  crear:      (ruta, datos)        => apiRequest('POST',   `/${ruta}`, datos),
  actualizar: (ruta, id, datos)    => apiRequest('PUT',    `/${ruta}/${id}`, datos),
  eliminar:   (ruta, id)           => apiRequest('DELETE', `/${ruta}/${id}`),
};

Alpine.start();
