# Architecture de Curlmetry

```
        +--------------------+
        |  TracerProvider    |
        +---------+----------+
                  |
        +---------v----------+
        |      Tracer        |  <---->  GlobalTracer::get()
        +---------+----------+
                  |
        +---------v----------+
        |   SpanBuilder       |
        +---------+----------+
                  |
        +---------v----------+
        |       Span         |---+
        +---------+----------+   |
                  |              |
        +---------v----------+   |
        |     SpanProcessor  |<--+
        +---------+----------+
                  |
        +---------v----------+
        |     Exporter       |
        +--------------------+

Exporte vers : OTLP / Jaeger / Debug

Compliant :
- PSR-18 : CurlHttpClient
- PSR-7  : Request / Response / Stream
```
