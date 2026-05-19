const button = document.querySelector(".message_close")
const message = document.querySelector(".message_area")

function close_message(){

    const message = button.closest(".message")
    if (message){
        message.remove()
    }


}



button.addEventListener("click", close_message)

setTimeout(()=>{
    message.remove();
},3000)

