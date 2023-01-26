const loaderComponent = document.getElementById('loader')
const createInput = document.getElementById('create-input')
const tasksTable = document.getElementById('tasks-table')
const pagination = document.getElementById('pagination')
const loader = document.getElementById('loader')
const store = createStore()
const modal = new bootstrap.Modal('#modal')
const modalElement = document.getElementById('modal')
const modalInputElement = modalElement.querySelector('input[type="text"]')
const modalInputId = modalElement.querySelector('input[type="hidden"]')
const alertContainer = document.getElementById('alert-container')
const emptyP = document.querySelector('p#empty')

function createStore() {
    const linksCount = 3
    const tasksOnPage = 5
    const subscribers = []

    let pages = []
    let tasks = []
    let isLoading = false
    let updatedId, removedId
    let modalInputContent = ''
    let showModal = false
    let alertContent = ''
    let showAlert = false
    let page = 1, tasksCount

    return {
        get alertContent() {
            return alertContent
        },
        get tasks() {
            return tasks
        },
        get isLoading() {
            return isLoading
        },
        get modalInputContent() {
            return modalInputContent
        },
        get page() {
            return page
        },
        get pages() {
            return pages
        },
        get updatedId() {
            return updatedId
        },
        get removedId() {
            return removedId
        },
        get showAlert() {
            return showAlert
        },
        get showModal() {
            return showModal
        },
        set page(val) {
            page = val
        },
        set tasks(newTasks) {
            tasks = newTasks
            subscribers.forEach(item => item())
        },
        set isLoading(val) {
            isLoading = val
            subscribers.forEach(item => item())
        },
        set removedId(val) {
            removedId = val
            const removedTask = tasks.find(({ id }) => id === removedId)
            if ( removedTask ) {
                alertContent = 'Удалить задачу ' + removedTask.text + '?'
                showAlert = true
            } else {
                alertContent = ''
                showAlert = false
                removedId = undefined
            }
            subscribers.forEach(item => item())
        },
        set tasksCount(val) {
            tasksCount = val
            const allPages = Math.ceil(tasksCount / tasksOnPage)
            const lftShift = Math.floor(linksCount / 2)
            const rghtShift = linksCount - lftShift
            const startNumberPage = Math.max(1, page - lftShift)
            const lastNumberPage = Math.min(allPages, page + rghtShift)
            const queryParams = new URLSearchParams(location.search)

            if (page != 1 && page > lastNumberPage) {
                let search = '?'
                for (let [key, val] of queryParams.entries()) {
                    if ( key === 'page' ) {
                        val = lastNumberPage
                    }
                    search += `${key}=${val}`
                }

                location.search = search
            }

            pages = []
            for (let i = startNumberPage; i <= lastNumberPage; ++i) {
                pages.push(i)
            }
            subscribers.forEach(item => item())
        },
        set updatedId(val) {
            updatedId = val
            const updatedTask = tasks.find(({ id }) => id === updatedId)
            if ( updatedTask ) {
                modalInputContent = updatedTask.text
                showModal = true
            } else {
                modalInputContent = ''
                showModal = false
                updatedId = undefined
            }
            subscribers.forEach(item => item())
        },
        subscribe(fn) {
            subscribers.push(fn)
        }
    }
}

async function start() {
    const queryParams = new URLSearchParams(location.search)
    store.page = queryParams.get('page') ?? 1

    const url = `/tasks/get?page=${store.page}`
    store.isLoading = true
    const send = fetch(url).then(data => data.json()).then(data => data)
    const [tasksData] = await Promise.all([send, delay(400)])
    await Promise.all([send, delay(400)])
    store.isLoading = false
    if ( tasksData ) {
        store.tasks = tasksData.tasks
        store.tasksCount = tasksData.count
    }
}

async function sendData(htmlFromElement) {
    const method = htmlFromElement.dataset.method ?? htmlFromElement.method
    const url = htmlFromElement.action + `?page=${store.page}`

    if ( !(url && method) ) {
        console.error('Отсутствуют необходимые атрибуты формы')
        return
    }
    
    const body = new FormData(htmlFromElement)

    try {
        const response = await fetch(url, {
            body, method
        })
        const data = await response.text()
        try {
            return JSON.parse(data)
        } catch (e) {
            console.log(data)
            throw new Error(data)
        }
    } catch(e) {
        if ( store ) {
            store.isLoading = false
        }
        
        console.log(e)
    }
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

function render() {
    const { alertContent, isLoading, page, pages, tasks, modalInputContent, removedId, showAlert, showModal, updatedId } = store

    if ( loader ) {
        if ( isLoading ) {
            loader.classList.remove('d-none')
        } else {
            loader.classList.add('d-none')
        }
    }

    if (tasksTable?.querySelector('tbody')) {
        if ( tasks.length ) {
            pagination?.classList.remove('d-none')
            tasksTable.classList.remove('d-none')
            emptyP?.classList.add('d-none')
            tasksTable.querySelector('tbody').innerText = ''
            tasks.forEach(({ id, text, date, important, complete }) => {
                const importantRowStyle = important === '1' ? "fw-bold" : ""
                const completedRowStyle = complete === '1' ? "text-muted" : ""
                const importantChecked = important === '1' ? "checked" : ""
                const completeChecked = complete === '1' ? "checked" : ""
                const html = `
                    <tr class="${importantRowStyle} ${completedRowStyle}">
                        <td class="align-middle">${text}</td>
                        <td class="text-center align-middle">${date}</td>
                        <td class="text-center align-middle">
                            <form method="POST" action="/tasks/important">
                                <input type="hidden" name="id" value="${id}">
                                <label class="form-check-label d-flex justify-content-center align-items-center">
                                    <input
                                        class="form-check-input me-1"
                                        type="checkbox"
                                        name="important" ${importantChecked}
                                        onchange="inputChangeHandler(this)"
                                    >
                                    <span>важное</span>
                                </label>
                            </form>
                        </td>
                        <td class="text-center align-middle">
                            <form method="POST" action="/tasks/complete">
                                <input type="hidden" name="id" value="${id}">
                                <label class="form-check-label d-flex justify-content-center align-items-center">
                                    <input
                                        class="form-check-input me-1"
                                        type="checkbox"
                                        name="complete" ${completeChecked}
                                        onchange="inputChangeHandler(this)"
                                    >
                                    <span>выполнено</span>
                                </label>
                            </form>
                        </td>
                        <td class="text-center align-middle">
                            <button
                                class="btn btn-primary btn-sm"
                                name="${id}"
                                onclick="updateHandler(this)"
                            >Изменить</button>
                        </td>
                        <td class="text-center align-middle">
                            <button class="btn btn-link text-danger btn-sm" name="${id}" onclick="rmHandler(this)">Удалить</button>
                        </td>
                    </tr>
                `
                tasksTable.querySelector('tbody').insertAdjacentHTML('beforeend', html)
            })
        } else {
            tasksTable.classList.add('d-none')
            emptyP?.classList.remove('d-none')
            pagination?.classList.add('d-none')
        }
    }

    if ( pagination?.querySelector('ul') ) {
        const ul = pagination.querySelector('ul')
        ul.textContent = ''
        const prevHtml = `<li class="page-item ${page == 1 ? "disabled" : ""}">
            <a class="page-link" href="/tasks?page=${+page - 1}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>`
        const nextHtml = `<li class="page-item ${page == pages.length ? "disabled" : ""}">
            <a class="page-link" href="/tasks?page=${+page + 1}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>`

        ul.insertAdjacentHTML('beforeend', prevHtml)
        pages.forEach((item) => {
            const html = `<li class="page-item ${item == page ? "active" : ""}">
                <a class="page-link" href="/tasks?page=${item}">${item}</a>
            </li>`
            ul.insertAdjacentHTML('beforeend', html)
        })
        ul.insertAdjacentHTML('beforeend', nextHtml)
    }

    if ( isLoading ) {
        return
    }

    if ( showModal ) {
        modalInputElement.value = modalInputContent
        modalInputId.value = updatedId
        modal.show()
    } else {
        modal.hide()
    }

    if (showAlert && alertContent && alertContainer) {
        alertContainer.innerHTML = `<form class="alert alert-danger" role="alert" method="POST" action="/tasks/remove"> 
            <div class="d-flex align-items-center">
                <span>${alertContent}</span>
                <input type="hidden" name="id" value="${removedId}">
                <button type="submit" class="ms-auto btn btn-link text-danger">Да</button>
                <button type="button" class="btn btn-link text-muted" onclick="cancelRmHandler()">Нет</button>
            </div>
        </form>`
    } else {
        alertContainer.innerHTML = ''
    }
}

function clean() {
    store.removedId = undefined
    store.updatedId = undefined
    if ( createInput ) {
        createInput.value = ''
    }
}

async function submitHanlder(event) {
    event.preventDefault()
    
    const send = sendData(event.target).then(data => data)
    store.isLoading = true
    const [tasksData] = await Promise.all([send, delay(400)])
    store.isLoading = false
    if ( tasksData ) {
        store.tasks = tasksData.tasks
        store.tasksCount = tasksData.count
        clean()
    }
}

async function inputChangeHandler(htmlInputElement) {
    const form = htmlInputElement.closest('form')
    if ( !form ) {
        console.error('Форма не найдена')
        return
    }
    store.isLoading = true
    const send = sendData(form).then(data => data)
    const [tasksData] = await Promise.all([send, delay(400)])
    store.isLoading = false
    if ( tasksData ) {
        store.tasks = tasksData.tasks
        store.tasksCount = tasksData.count
    }
}

function updateHandler(htmlButtonElement) {
    store.updatedId = htmlButtonElement.name
}

function rmHandler(htmlButtonElement) {
    store.removedId = htmlButtonElement.name
}

function cancelRmHandler() {
    store.removedId = undefined
}

store.subscribe(render)

start()

addEventListener('submit', submitHanlder)