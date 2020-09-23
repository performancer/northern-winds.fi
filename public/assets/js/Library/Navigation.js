export {Navigation};

class Navigation
{
    constructor(container)
    {
        let labels = container.querySelectorAll('.evt-submenu-label');
        for (let label of labels) {
            label.addEventListener('click', () => {
                let checkboxes = container.querySelectorAll('.evt-submenu-checkbox');
                for (let checkbox of checkboxes) {
                    if (label.htmlFor !== checkbox.id) {
                        checkbox.checked = false;
                    }
                }
            });
        }
    }
}