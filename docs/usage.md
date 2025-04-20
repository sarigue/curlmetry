# 📘 Utilisation de Curlmetry

Voici un exemple complet d'utilisation de Curlmetry dans une application PHP 5.6+ :

## 🔧 Initialisation du Tracer

```php
use Curlmetry\Exporter\DebugExporter;use Curlmetry\Processor\SimpleSpanProcessor;use Curlmetry\Sampling\AlwaysOnSampler;use Curlmetry\TracerProvider;

$exporter = new DebugExporter();
$processor = new SimpleSpanProcessor($exporter);
$sampler = new AlwaysOnSampler();

$provider = new TracerProvider($sampler, $processor);
$tracer = $provider->getTracer('my-service');
```

## 🛠 Création d’un span manuel

```php
$span = $tracer->startSpan('my.operation');
// ... du code ...
$tracer->endSpan($span);
```

## ✅ Avec un span actif (startActiveSpan)

```php
$tracer->startActiveSpan('my.request', function ($span) {
    $span->setAttribute('http.method', 'GET');
    // ... code ...
});
```

## 🧼 Fermeture automatique à la fin

```php
register_shutdown_function(function () use ($provider) {
    $provider->shutdown();
});
```

## 🌍 Propagation du contexte HTTP

```php
use Curlmetry\Propagation\TraceContextPropagator;

TraceContextPropagator::inject($span, $headers);
```
