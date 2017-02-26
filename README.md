# WPE CLI

Provides wp-cli access to your remote WP Engine installs

## Usage

Run wp-cli commands on your WP Engine installs from your local environment

```bash
$ wp wpe myinstall core version
```

## Installation

1. Open up the "advanced" tab in WP Engine Portal
2. Open the Network Inspector
3. Run a command in the wp-cli window
4. Look at the request that went out to `https://my.wpengine.com/installs`
5. From the request, get
	* The value for the Header X-CSRF-Token
	* The value for the Cookie __ar_v4
	* The value for the Cookie _session_id
6. Add these values to [your config file](https://make.wordpress.org/cli/handbook/config/#config-files) in the format below
7. Done!

```yaml
# Settings for the wpe-cli integration
wpe-cli:
  token: ABCDEFGHIJKLMNOP
  ar_v4: QRSTUVWYZ1234567890
  session_id: abcdefghijklmnop
```
