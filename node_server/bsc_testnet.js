require('dotenv').config();

const { Web3 } = require('web3'); 
const Tx = require('ethereumjs-tx').Transaction;
// const Web3 = require('web3');
const web3 = new Web3(process.env.BSC_RPC_URL);

const privateKey = Buffer.from('b540b92f28eb256d50b63f5d9d2420a21777ec96763160e7da2fc4163192359d', 'hex');
const addressFrom = '0x359Ee4038A43Df332405E90715998770Fbc2646E';
const addressTo = '0xE9F7B85b8A99F28B8E60be8B4851eaF3cB437402';

//binance wallet: 0x359Ee4038A43Df332405E90715998770Fbc2646E
//eth wallet 0xE9F7B85b8A99F28B8E60be8B4851eaF3cB437402

let data =  JSON.stringify({ 
  company: "sample name", 
  or_number: "2029302", 
  total_amount:5000, 
  tax:600
})

const bufferText = Buffer.from(data, 'utf8');
// console.log(web3.utils.toWei('0', 'ether'))

async function send_transaction(){
  const nonce = await web3.eth.getTransactionCount(addressFrom);
  const gasPrice = await web3.eth.getGasPrice();
  const transactionData = {
    nonce:nonce,
    from: addressFrom,
    to: addressTo,
    // value: web3.utils.toHex(web3.utils.toWei('1000', 'ether')),
    value: web3.utils.toHex(web3.utils.toWei('0.0', 'ether')),
    gasLimit: web3.utils.toHex(700000),
    gasPrice: gasPrice,
    data: bufferText
  };


  web3.eth.accounts.signTransaction(transactionData, privateKey)
    .then((signedTx) => {
      // console.log(signedTx)
      web3.eth.sendSignedTransaction(signedTx.rawTransaction)
        .on('transactionHash', (hash) => {
          console.log('Transaction hash:', hash);
        })
        .on('receipt', (receipt) => {
          console.log('Transaction receipt:', receipt);
        })
        .on('error', (error) => {
          console.error('Error:', error);
        });
  });

}

  const balances = async () => {
    const balanceFrom = web3.utils.fromWei(
       await web3.eth.getBalance(addressFrom),
       'ether'
    );
    const balanceTo = await web3.utils.fromWei(
       await web3.eth.getBalance(addressTo),
       'ether'
    );
 
    console.log(`The balance of ${addressFrom} is: ${balanceFrom} BNB.`);
    console.log(`The balance of ${addressTo} is: ${balanceTo} BNB.`);
 };
 
 balances();

 send_transaction();