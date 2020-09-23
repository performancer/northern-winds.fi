export {AjaxForm};

class AjaxForm
{
    constructor(container)
    {
        this.container = container;
    }

    addEvents()
    {
        let form = this.container.querySelector('form');

        if (form.addEventListener) {
            form.addEventListener("submit", (e) => {
                e.preventDefault();

                let xml = new XMLHttpRequest();
                xml.open("POST", e.target.action, true);

                xml.onreadystatechange = () => {
                    if (xml.readyState === XMLHttpRequest.DONE) {
                        let message = document.createElement("h4");
                        message.innerHTML = xml.response;

                        if(xml.status >= 200 && xml.status < 400) {
                            this.container.innerHTML = '';
                            this.container.classList.add('container');
                            this.container.appendChild(message);
                        } else {
                            let error = form.querySelector('#error');
                            error.innerHTML = '';
                            message.style.color = 'darkred';
                            error.appendChild(message);
                        }
                    }
                };

                let data = new FormData(e.target);
                xml.send(data);
            }, true);
        }
    }
}