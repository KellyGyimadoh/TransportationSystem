import PaystackPop from '@paystack/inline-js'
const alertbox=document.querySelector(".alertbox");
if(alertbox){
    setTimeout(()=>{
        alertbox.classList.add("hidden")
    },3000)
}
document.addEventListener('livewire:init', () => {
      
    setInterval(() => {
        Livewire.dispatch('autoNext');
    }, 3000);
});