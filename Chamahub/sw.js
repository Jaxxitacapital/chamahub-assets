const CACHE_NAME = "chamahub-cache-v1";
const urlsToCache = [
  "/Chamahub/",
  "/Chamahub/index.php?i=1",
  "/Chamahub/css/style.css",
  "/Chamahub/bootstrap.min.css",
  "/Chamahub/fallback.jpg"
];

// 🔃 Install cache
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// 🎯 Fetch & fallback
self.addEventListener("fetch", event => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request).then(response => {
      return response || caches.match("/Chamahub/fallback.jpg");
    }))
  );
});
