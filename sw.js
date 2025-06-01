self.addEventListener('install', event => {
  console.log('Service Worker instalado');
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  console.log('Service Worker ativado');
});

self.addEventListener('push', event => {
  console.log('Push recebido:', event);

  let data = {};
  if (event.data) {
    data = event.data.json();
  }

  const title = data.title || 'Nova Notificação';
  const tipo = data.tipo || 'padrao'; // tipo pode ser: imagem, acao, silenciosa, etc.

  const baseOptions = {
    body: data.body || 'Você tem uma nova mensagem.',
    icon: data.icon || '/icon.png',
    badge: data.badge || '/badge.png',
    data: {
      url: data.data?.url || '/'
    }
  };

  // Personaliza conforme o tipo
  switch (tipo) {
    case 'imagem':
      baseOptions.image = data.image || '/banner.jpg'; // grande imagem
      baseOptions.requireInteraction = true;
      break;

    case 'acao':
      baseOptions.actions = [
        {
          action: 'abrir',
          title: 'Abrir agora',
          icon: '/icons/abrir.png'
        },
        {
          action: 'ignorar',
          title: 'Ignorar',
          icon: '/icons/ignorar.png'
        }
      ];
      break;

    case 'silenciosa':
      baseOptions.silent = true;
      baseOptions.tag = 'atualizacao';
      break;

    default:
      // tipo "padrao"
      break;
  }

  event.waitUntil(
    self.registration.showNotification(title, baseOptions)
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  const destino = event.notification.data?.url || '/';

  // Se for ação "ignorar", não abre nada
  if (event.action === 'ignorar') return;

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
      for (const client of clientList) {
        if (client.url === destino && 'focus' in client) {
          return client.focus();
        }
      }
      return clients.openWindow(destino);
    })
  );
});
