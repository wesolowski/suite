# Spryker Suite
[![Build Status](https://github.com/spryker-shop/suite/workflows/CI/badge.svg)](https://github.com/spryker-shop/suite/actions?query=workflow%3ACI)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)

License: [MIT](LICENSE)

## Docker installation

* [Troubleshooting](https://academy.spryker.com/getting_started/troubleshooting.html)

## Docker installation

For detailed installation instructions of Spryker in Docker, see [Getting Started with Docker](https://documentation.spryker.com/docs/getting-started-with-docker).

For troubleshooting of Docker based instances, see [Troubleshooting](https://documentation.spryker.com/docs/spryker-in-docker-troubleshooting).

### Prerequisites

For the installation prerequisites, see [Docker Installation Prerequisites](https://documentation.spryker.com/docs/docker-installation-prerequisites).

Recommended system requirements for MacOS:

|Macbook type|vCPU| RAM|
|---|---|---|
|15' | 4 | 6GB |
|13' | 2 | 4GB |

### Installation

Run the commands:

```bash
# clone suite
cd suite
git https://github.com/spryker/docker-sdk.git docker
```

### Developer environment

1. Run the commands right after cloning the repository:

```bash
docker/sdk boot deploy.dev.yml
```


```bash
docker/sdk up -x
```

### Troubleshooting

**No data on Storefront**

Use the following services to check the status of queues and jobs:
- queue.spryker.local
- scheduler.spryker.local

**Fail whale**

1. Run the command:
```bash
docker/sdk logs
```
2. Add several returns to mark the line you started from.
3. Open the page with the error.
4. Check the logs.


**Errors**

`ERROR: remove spryker_logs: volume is in use - [{container_hash}]`

1. Run the command:
```bash
docker rm -f {container_hash}
```
2. Repeat the failed command.

`Error response from daemon: OCI runtime create failed: .... \\\"no such file or directory\\\"\"": unknown.`

Repeat the failed command.
