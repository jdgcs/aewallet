# aeWallet
aeWallet: A PC-based wallet of Aeternity.

The binary release: https://github.com/jdgcs/aewallet/releases/tag/1.0.0

百度网盘地址:
1. Windows: https://pan.baidu.com/s/1eEscxHn2EF_2FAHB8npXwA Code: tqtk
2. Ubuntu: https://pan.baidu.com/s/1Y6EVHI7FM9r1voMBOYADvA Code: vrqx

## Features
1. ONLINE and OFFLINE transactions can be made.
2. Support local and remote fullnode.
3. Basic transaction with payload.


## Run
### Windows
Run: **aeWallet.bat**
### Ubuntu 18.04+
1. apt install php php-curl php-gd
2. chmod 777 start.sh
3. **./start.sh**

## Notice
1. Please put the wallet to the root directory such as D:\  **without** special characters in the path.
2. With long payload, please set the gas fee manually, such as 0.01 AE.


## Build from sourcecode - Windows

1. Compile https://github.com/aeternity/aepp-sdk-go, rename aepp-sdk-go.exe to signtx.exe, and copy it the the ./env directory.
2.  Download PHP from https://windows.php.net/download#php-7.3, and extract them to the ./env directory, enable culr extension.
3. Run: **aeWallet.bat**
