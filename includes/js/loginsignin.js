
const input_html = document.querySelectorAll("input");
for(let input of input_html){
    input.addEventListener("keyup", handleLabel);
    console.log(input);
}


function handleLabel(e){
    const input = e.currentTarget;
    const label = document.querySelector('label[for="'+input.id+'"]');
    if(input.value == ""){
        label.style.display = "none";
    }else{
        label.style.display = "block";
    }
    console.log("change")
}