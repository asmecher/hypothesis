let maxCharNumber = 300;
let annotationTargets = document.querySelectorAll('.annotation_target > blockquote');
let annotationContents = document.querySelectorAll('.annotation_content > blockquote');

annotationTargets.forEach(target => {
    addReadMoreLogic(target);
});

annotationContents.forEach(content => {
    addReadMoreLogic(content);
});

function addReadMoreLogic(element) {
    if(element.textContent.length <= maxCharNumber) {
        let readMoreBtn = element.nextElementSibling;
        readMoreBtn.style.display = "none";
    }
    else {
        let trimmedText = element.textContent.trim();
        let textToDisplay = trimmedText.slice(0, maxCharNumber);
        let textMore = trimmedText.slice(maxCharNumber);
        element.innerHTML = `${textToDisplay}<span class="dots">...</span><span class="more hide">${textMore}</span>`;
    }
}

function toggleReadMore(btn){
    let parent = btn.parentElement;
    parent.querySelector('.dots').classList.toggle('hide');
    parent.querySelector('.more').classList.toggle('hide');
    if(btn.classList.contains('read_more'))
        btn.nextElementSibling.classList.remove('hide');
    else
        btn.previousElementSibling.classList.remove('hide');
    btn.classList.add('hide');
}