function modalAction(elemnt){
    const moalMain= document.querySelector(elemnt);
      if(moalMain.classList.contains('active')){
      moalMain.classList.remove('active');
      }else{
      moalMain.classList.add('active');
      }
  }


  function receiveSubmitView(open=1){
    const ModalTwo = document.querySelector('.modal-dialog-seven');
    const ModalSix = document.querySelector('.modal-dialog-six');
    if(open===1){
      ModalTwo.classList.add('active');
      ModalSix.classList.remove('active');
    }else if(open===2){
      ModalTwo.classList.remove('active');
      ModalSix.classList.add('active');
    }
    else{
      ModalSix.classList.remove('active');
    }
  }








