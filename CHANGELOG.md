# Changelog

## v1.1.1

- Made `ServerRequest` and `ResponseFactory` Services
- Initialize `ServerRequest` in `HttpBrick`, then add it to Services
- Don't try to check size of `php://input`

## v1.1.0

*2023-04-25*

- Rm `guzzlehttp/psr7` and implement PSR-7 and PSR-17

## v1.0.0

*2023-04-10*

- Add wrap around `guzzlehttp/psr7`
