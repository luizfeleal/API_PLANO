
// FUNÇÃO PARA FECHAR MODAL
function fechaModal(){
  document.getElementById('modal').style.visibility = "hidden";
  document.getElementById('modal').style.opacity = "0";
  document.getElementById('message').innerText = "";
}

// CRIA UMA DIV PARA ADICIONAR DADOS DENO NOVOS BENEFICIÁRIOS
function createDiv(i){
  var elemento = document.createElement('div');
  elemento.setAttribute('class', 'text-container');
  elemento.setAttribute('id', `text-container${i}`);
  document.getElementById('content').appendChild(elemento);
}

// CRIA O CAMPO NOME E INSERE NA DIV
function createFieldName(i){
  //createDiv(i);
  var elemento1 = document.createElement('label');
  elemento1.setAttribute('for', 'nome');
  elemento1.textContent = 'Nome:'

  var elemento2 = document.createElement('input');
  elemento2.setAttribute('type', 'text');
  elemento2.setAttribute('name',  'nome');
  elemento2.setAttribute('id', `nome${i}`);

  document.getElementById(`text-container${i}`).appendChild(elemento1);
  document.getElementById(`text-container${i}`).appendChild(elemento2);

}

//CRIA O CAMPO IDADE E INSERE NA DIV
function createFieldIdade(i){

  var elemento1 = document.createElement('label');
  elemento1.setAttribute('for', 'idade');
  elemento1.textContent = 'Idade:'

  var elemento2 = document.createElement('input');
  elemento2.setAttribute('type', 'text');
  elemento2.setAttribute('name',  'idade');
  elemento2.setAttribute('id', `idade${i}`);


  document.getElementById(`text-container${i}`).appendChild(elemento1);
  document.getElementById(`text-container${i}`).appendChild(elemento2);

}

//FUNÇÃO QUE FAZ O ENVIO DA REQUISIÇÃO 

function enviaFetch() {

    
          const url = 'http://localhost/API_PLANO/public/api/user';
          //INSTANCIA OBJETO
          var input = new Object();
      
          
          input.registro =  document.getElementById('registro').value;
          input.qtd_beneficiarios = document.getElementById('qtd_beneficiarios').value;

          // ARRAY COM OS DADOS DO BENEFICIÁRIO
          input.beneficiarios = [{
            nome: document.getElementById('nome').value,
            idade: document.getElementById('idade').value,
          }
          ]
         
          //ADICIONA AO ARRAY OS DADOS DOS OUTROS BENEFICIÁRIOS, CASO HAJA
          for(let q = 0; q < (document.getElementById('qtd_beneficiarios').value - 1); q++){
            input.beneficiarios.push({nome: document.getElementById(`nome${q}`).value, idade: document.getElementById(`idade${q}`).value})
          }
          const optionsRequisicao = {
            method: 'POST',
            body: JSON.stringify(input)
          }

          fetch(url, optionsRequisicao)

          .then((res)=>{ if(res.status === 200 || res.status === 201 || res.status === 'ok') {
            return res.json()
          }else{
            throw new Error(res.status);
          }
          })
          .then((data)=> {
            var altura = 220

            var elemento1 = document.createElement('p');
            elemento1.setAttribute('class', 'modal-message');
            elemento1.textContent = 'Aqui está o resultado para o plano escolhido:'
            document.getElementById(`message`).appendChild(elemento1);

            //PERCORRE O LOOP PARA PEGAR AS INFORMAÇÕES E EXIBIR NA TELA.
            for(let q = 0; q < document.getElementById('qtd_beneficiarios').value; q++){

            var elemento2 = document.createElement('p');

            elemento2.setAttribute('class', 'modal-message');
            elemento2.textContent = `Idade: ${data.message[q].idade} anos    Valor: R$${data.message[q].valor}`

            document.getElementById(`message`).appendChild(elemento2);
            document.getElementById('modal-popup-sucess').style.height = `${altura += 40}px`
          }

            var elemento3 = document.createElement('p');
            elemento3.setAttribute('class', 'modal-message');
            elemento3.textContent = `Valor total: R$ ${data.message[(document.getElementById('qtd_beneficiarios').value)].valorTotal}`
            document.getElementById(`message`).appendChild(elemento3);

            document.getElementById('modal').style.visibility = "visible";
            document.getElementById('modal').style.opacity = "1";
          })
          .catch(()=>{
            var elemento4 = document.createElement('p');
            elemento4.setAttribute('class', 'modal-message-error');
            elemento4.textContent = `Houve um erro ao buscar o registro do plano, verifique se todas as informações estão preenchidas corretamente.`
            document.getElementById(`message`).appendChild(elemento4);

            document.getElementById('modal').style.visibility = "visible";
            document.getElementById('modal').style.opacity = "1";
          });
        
        }

        //PEGA OS EVENTOS DE CLIQUE DOS BOTÕES
        document.getElementById('botaoEnviar').addEventListener('click', function(){
          if(document.getElementById('nome').value === '' || document.getElementById('idade').value === '' || document.getElementById('qtd_beneficiarios').value === ''){
            var elemento5 = document.createElement('p');
            elemento5.setAttribute('class', 'modal-message-error');
            elemento5.textContent = `Todas as informações devem ser preenchidas corretamente.`
            document.getElementById(`message`).appendChild(elemento5);

            document.getElementById('modal').style.visibility = "visible";
            document.getElementById('modal').style.opacity = "1";
          }else{
          enviaFetch()
          }
        });
        document.getElementById('fecha-modal').addEventListener('click', fechaModal);

        //PEGA O EVENTO DE MUDANÇA NO INPUT QUE RECEBE A INFORMAÇÃO DA QUANTIDADE DE BENEFICIÁRIOS.
        document.getElementById('qtd_beneficiarios').addEventListener('change', function(){
          var altura = 400;
          for(let i = 0; i < (document.getElementById('qtd_beneficiarios').value - 1); i++){
            //if(document.getElementById('qtd_beneficiarios').value < 4){
              createDiv(i);
              document.getElementById(`text-container${i}`).innerText = "";
              createFieldName(i);
              createFieldIdade(i);
            document.getElementById('all-content').style.height = `${altura += 140}px`
          //}
        }
      
      });