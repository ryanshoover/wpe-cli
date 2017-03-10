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
SUCCESS: Cache flushed!
```

Trigger a backup checkpoint on your WP Engine install

```bash
$ wp wpe backup myinstall
SUCCESS: Backup triggered! This can take a while! You will be notified at ryan.hoover@wpengine.com when the checkpoint has completed.
```

## Installation

1. Open up the "advanced" tab in WP Engine Portal
2. Open the Network Inspector
3. Run a command in the wp-cli window
4. Look at the request that went out to `https://my.wpengine.com/installs`
5. From the request, get
	* The value for the Header X-CSRF-Token
	* The value for the Cookie _session_id
6. Add these values to [your config file](https://make.wordpress.org/cli/handbook/config/#config-files) in the format below
7. Done!

```yaml
# Settings for the wpe-cli integration
wpe-cli:
  token: ABCDEFGHIJKLMNOP
  session_id: abcdefghijklmnop
```

### Pro tip

You can shorten `$ wp wpe ...` to just `$ wpe ...`. Just add this line to your `~/.bash_profile` or `~/.bashrc` and restart your shell window.

```bash
alias wpe='wp wpe' # shortcut alias for wpe-cli tool
```
