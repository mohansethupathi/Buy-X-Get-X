function formSubmit(){

    const my_subject = document.getElementById("subject");
    const my_body = document.getElementById("message");

    const my_btn = document.getElementById("submitBtn").addEventListener('click',()=>{
        const form = document.getElementById('form');
        const formData = new FormData(form);
        formData.append("action","my_action")
        
        if(my_subject.value!="" && my_body.value!=""){

            fetch(ajaxurl, {
                method: 'POST',
                body: formData,
                dataType:"json"
            })
            .then((response) => response.json())
            .then(json => {
                try {
                  //console.log(json);
                  if(json.success == true)
                  {
                    alert('Mail Sent Successfully');
                  }else{
                    alert('Invalid Email Address')
                  }
                  
                } catch(err) {
                    alert('Mail Sending Error')
                }
              });

        }else{
            alert("please fill All required Boxes");
        }

    })
}

window.addEventListener('load', () => {
    formSubmit();
})