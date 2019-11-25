
## Fetch Dependencies

If bower is installed you should be able to fetch missing dependencies using `bower install`. Make sure you are in the right directory while running the command.

## Configuration

If the config files `manifest.json` and `schedule.json` you can create them using the following commands.

```
cd schedule/
mv manifest.json.example manifest.json
mv schedule.json.example schedule.json
```

Make sure that the `apiBasePath` inside `schedule/schedule.json` is pointing to the `index.php` file in `public/api`



Example `apiBasePath`:

```json
{
  "apiBasePath":"/~vplan/api/index.php"
}
```

Make sure that the `start_url` inside `schedule/manifest.json` is pointing to the `index.html`.

Example `start_url`:

```json
{
  "apiBasePath":"/~vplan/api/index.php"
}
```


## Build The Application

First, make sure you have the [Polymer CLI](https://www.npmjs.com/package/polymer-cli) installed. Run the following commands to build the application.

```
cd schedule/
polymer build
```

This will create builds of your application in the `build/` directory, optimized to be served in production.

Move it to your public directory. Make sure the relatives paths you the configurations you made earlier are still valid.
