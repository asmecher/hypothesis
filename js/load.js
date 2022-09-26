let maxCharNumber = 300;
let annotationTargets = document.querySelectorAll('.annotation_target > blockquote');

annotationTargets.forEach(target => {
    if(target.textContent.length <= maxCharNumber) {
        let readMoreBtn = target.nextElementSibling;
        readMoreBtn.style.display = "none";
    }
    else {
        let trimmedText = target.textContent.trim();
        let textToDisplay = trimmedText.slice(0, maxCharNumber);
        let textMore = trimmedText.slice(maxCharNumber);
        target.innerHTML = `${textToDisplay}<span class="dots">...</span><span class="more hide">${textMore}</span>`;
    }
});

function toggleReadMore(btn){
    let annotation = btn.parentElement;
    annotation.querySelector('.dots').classList.toggle('hide');
    annotation.querySelector('.more').classList.toggle('hide');
    if(btn.classList.contains('read_more'))
        btn.nextElementSibling.classList.remove('hide');
    else
        btn.previousElementSibling.classList.remove('hide');
    btn.classList.add('hide');
}