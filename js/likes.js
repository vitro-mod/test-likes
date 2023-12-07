async function post_likes(post_id, type) {
    try {
        let response = await fetch(wp_ajax_url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ action: 'post_likes', post_id, type, url: document.location.href }),
        });

        if (response.ok) {
            let json = await response.json();
            return json;
        }
    } catch (error) {
        console.log('Fetch error: ', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.post__likes button').forEach(element => {
        element.addEventListener('click', post_likes_click);
    });

});

async function post_likes_click(event) {
    const target = event.target;
    const post_id = target.getAttribute('data-post-id');
    const isPressed = target.classList.contains('pressed');
    const rating = document.querySelector(`.post__rating[data-post-id="${post_id}"]`);

    const isLike = target.classList.contains('plus');
    let action = isLike ? 'like' : 'dislike';
    if (isPressed) {
        action = 'retract';
    }

    let response = await post_likes(post_id, action);

    if (response?.success) {
        rating.innerText = response.new_rating;
        document.querySelectorAll(`.post__likes button[data-post-id="${post_id}"]`).forEach(element => {
            element.classList.remove('pressed');
        });

        if (action != 'retract') {
            target.classList.add('pressed');
        }
    }

}
