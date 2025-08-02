const info = {
    'soft_example': {
        'prices': {
            'mini': '950 руб - 1 месяц',
            'full': '1950 руб - 3 месяца',
            'max': '1950 руб - 6 месяцев'
        },
        'date-end': {
            'mini': '01.09.2025',
            'full': '01.12.2025',
            'max': '01.06.2026'
        }
    },
    'learning_example': {
        'prices': {
            'mini': '950 руб - 1 месяц',
            'full': '1950 руб - 3 месяца',
            'max': '1950 руб - 6 месяцев'
        },
        'date-end': {
            'mini': '01.09.2025',
            'full': '01.12.2025',
            'max': '01.06.2026'
        }
    }
}

function init_info() {
    const object = document.querySelector('[data-info]')
    const priceList = object.querySelector('[data-prices]')
    const infoId = info[object.getAttribute('data-info')]
    Object.keys(infoId['prices']).forEach(price => {
        const priceElement = document.createElement('h4')
        priceElement.setAttribute('data-price', price)
        priceElement.textContent = infoId['prices'][price]
        priceList.appendChild(priceElement)
    })

    const faq = object.querySelector('[data-faq]');
    let faqCreated = false
    Object.keys(infoId['info']['faq']).forEach(faqBlock => {
        const faqElement = document.createElement('li')
        faqElement.classList.add('accordion', 'block', 'active-block')
        faqElement.innerHTML = `
        <div class="acc-btn ${faqCreated ? 'active' : ''}">
            <div class="icon-box"><i class="fa-solid fa-question"></i></div>
            ${faqBlock}
        </div>
        <div class="acc-content current">
            <div class="content">
                <p>${infoId['info']['faq'][faqBlock]}</p>
                </div>
            </div>
            `
        faqCreated = true;
        faq.appendChild(faqElement)
    })
}

function init_prices() {
    const prices = document.querySelector('[data-prices]')
    const prices_items = prices.querySelectorAll('[data-price]')
    prices_items.forEach(price => {
        price.onclick = () => {
            prices_items.forEach(price => {
                price.classList.remove('active')
            })
            price.classList.add('active')
            // document.querySelector('[data-date-end]').textContent = info[document.querySelector('[data-info]').getAttribute('data-info')]['date-end'][price.getAttribute('data-price')] только с переменными и если существует data-date-end
            const dateEnd_node = document.querySelector('[data-date-end]')
            if (dateEnd_node) {
                const dataInfo = document.querySelector('[data-info]').getAttribute('data-info')
                const dateEnd = info[dataInfo]['date-end'][price.getAttribute('data-price')]
                dateEnd_node.textContent = dateEnd
            }
            prices.setAttribute('data-prices', price.getAttribute('data-price'))
        }
    })
}

init_prices()

document.querySelector(`[data-price="${document.querySelector('[data-prices]').getAttribute('data-prices')}"]`).onclick()