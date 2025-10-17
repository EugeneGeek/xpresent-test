const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"services.index":{"uri":"\/","methods":["GET","HEAD"]},"services.show":{"uri":"services\/{service}","methods":["GET","HEAD"],"parameters":["service"],"bindings":{"service":"id"}},"bookings.store":{"uri":"bookings","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
