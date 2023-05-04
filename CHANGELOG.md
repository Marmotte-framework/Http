# Changelog

## v1.1.4

- Fix missing protocol version in ResponseFactory

## v1.1.3

*2023-04-30*

- Exclude tests files in `.gitattributes`

## v1.1.2

*2023-04-30*

- Exclude tests dir from classmap

## v1.1.1

*2023-04-27*

- Made `ServerRequest` and `ResponseFactory` Services
- Initialize `ServerRequest` in `HttpBrick`, then add it to Services
- Don't try to check size of `php://input`

## v1.1.0

*2023-04-25*

- Rm `guzzlehttp/psr7` and implement PSR-7 and PSR-17

## v1.0.0

*2023-04-10*

- Add wrap around `guzzlehttp/psr7`
