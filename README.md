# WPE CLI

Provides wp-cli access to your remote WP Engine installs

## Usage

Run wp-cli commands on your WP Engine install from your local environment

```bash
$ wp wpe cli myinstall core version
4.7.3
```

Clear all of the caches on your WP Engine production install

```bash
$ wp wpe flush myinstall
Success: Cache flushed!
```

Trigger a backup checkpoint on your WP Engine install

```bash
$ wp wpe backup myinstall
Success: Backup triggered! This can take a while! You will be notified at ryan.hoover@wpengine.com when the checkpoint has completed.
```

Replace your local database with a fresh copy of a WP Engine install's database

```bash
$ wp wpe fetch-db myinstall
Success: Local database replaced with database from myinstall.
```

## Installation

### Option 1: Install this as a wp-cli package

1. Install any wp-cli package (if you haven't already done so)
	Ex: `wp package install anhskohbo/wp-cli-themecheck`
2. Edit `~/.wp-cli/packages/composer.json`
3. Add the `ryanshoover/wpe-cli` repo as a requirement
4. Run `~/.wp-cli/packages/composer update`
5. Use the CLI commands from any WordPress install on your local machine

```json
	"require": {
        "ryanshoover/wpe-cli": "dev-master",
    },
	"repositories": {
        "ryanshoover/wpe-cli": {
            "type": "vcs",
            "url": "https://github.com/ryanshoover/wpe-cli"
        }
    },
```

### Option 2: Install this as a plugin

1. Clone the repo into your plugins folder
  `git clone git@github.com:ryanshoover/wpe-cli.git ./wp-content/plugins/wpe-cli`
2. Enable the plugin
3. Use the CLI commands from just this WordPress install.

### Configure the authentication settings

1. Open up the "advanced" tab in WP Engine Portal
2. Open the Network Inspector
3. Run a command in the wp-cli window
4. Look at the request that went out to `https://my.wpengine.com/installs`
5. From the request, get
	* The value for the Header `X-CSRF-Token`
  * The value for the Cookie `__ar_v4`
	* The value for the Cookie `_session_id`
6. Add these values to [your config file](https://make.wordpress.org/cli/handbook/config/#config-files) in the format below
7. Done!

```yaml
# Settings for the wpe-cli integration
wpe-cli:
  token: ABCDEFGHIJKLMNOP
  ar_v4: QRSTUVWYZ1234567890
  session_id: abcdefghijklmnop
```

### Pro tip

You can shorten `$ wp wpe ...` to just `$ wpe ...`. Just add this line to your `~/.bash_profile` or `~/.bashrc` and restart your shell window.

```bash
alias wpe='wp wpe' # shortcut alias for wpe-cli tool
```
