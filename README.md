# Captured for PHP

This is a rewrite of the [original](https://github.com/csexton/captured-php) Captured PHP script for use with [Captured](http://www.capturedapp.com/).

### Features

- Clean, modernized rewrite with PSR-2.
- External configuration.
- Uses [Bulletproof](https://github.com/samayo/bulletproof) to securely (and properly) handle image uploads.
- Customizable screenshot filenames with the ability to set a slug and timestamp (e.g. `Screenshot_2019-03-02_13-12-57.png`) or as a randomized string using the [Hashids](https://github.com/ivanakimov/hashids.php) library.

### Installation

```sh
$ composer create-project log1x/captured:dev-master
```

### Usage

- Set configuration in `config.php`.
- Upload the `captured` folder contents to your server.
- Configure your Captured App with your URL and token.

### Testing

For testing purposes, you can use `curl`:

```sh
curl -i -X POST \
  -F "token=YOUR_TOKEN" \
  -F "file=@path/to/test.jpg" \
   https://example.com/screenshots/
```
