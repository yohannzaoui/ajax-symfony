function onClickBtnLike(event) {
    event.preventDefault();

    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function (response) {
        spanCount.textContent = response.data.likes;

        if (icone.classList.contains('fas')) {
            icone.classList.replace('fas','far');
        }
        else {
            icone.classList.replace('far', 'fas');
        }

    }).catch(function (error) {
        if (error.response.status === 403){
            window.alert("Vous devez être connecter pour liker un article");
        } else {
            window.alert("une erreur c'est produite");
        }
    });
}
document.querySelectorAll('a.js-like').forEach(function (link){
    link.addEventListener('click', onClickBtnLike)
})