export {AjaxButton};

class AjaxButton
{
    constructor(container, selector, url)
    {
        this.container = container;
        this.selector = selector;
        this.url = url;
    }

    addEvents()
    {
        let elements = this.container.querySelectorAll(this.selector);

        for(let element of elements) {
            if (element.addEventListener) {
                element.addEventListener('click', (e) => {
                    e.preventDefault();

                    let xml = new XMLHttpRequest();
                    xml.open('POST', this.url, true);

                    xml.onreadystatechange = () => {
                        if (xml.readyState === XMLHttpRequest.DONE) {
                            let notice = document.createElement('h4');
                            notice.innerHTML = xml.response;
                            this.container.appendChild(notice);
                        }
                    };

                    let data = new FormData();
                    data.append('id', e.target.dataset.id);
                    xml.send(data);

                    this.container.innerHTML = '';
                    this.container.classList.add('container');
                });
            }
        }
    }
}