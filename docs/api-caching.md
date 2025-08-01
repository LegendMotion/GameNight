# API Caching

Public GET endpoints send caching headers to reduce bandwidth and improve performance.

| Endpoint | Cache-Control | Validation |
| --- | --- | --- |
| `articles.php`, `article.php`, `games.php`, `game.php` (public games), `collection.php` | `public, max-age=3600` | `ETag` |

Clients should cache responses for up to one hour and revalidate using the returned `ETag`.  On a match the server responds with `304 Not Modified` and no body.

Administrative endpoints such as `auth.php` and `cleanup_tokens.php` include `Cache-Control: no-store` and must never be cached.
