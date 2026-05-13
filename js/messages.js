const button = document.querySelector(".message_close")

function close_message(){

    const message = button.closest(".message")
    if (message){
        message.remove()
    }


}



button.addEventListener("click", close_message)