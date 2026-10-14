const tradingViewWidgetSelector = '[data-tradingview-widget]';

const getTradingViewTheme = () => (document.documentElement.classList.contains('dark') ? 'dark' : 'light');

const loadTradingViewWidgets = () => {
    const theme = getTradingViewTheme();

    document.querySelectorAll(tradingViewWidgetSelector).forEach((widgetConfig) => {
        const container = widgetConfig.closest('.tradingview-widget-container');

        if (!container || widgetConfig.dataset.loadedTheme === theme) {
            return;
        }

        container.querySelectorAll('iframe, .tradingview-widget-container__widget > *, [data-generated-tradingview-widget]').forEach((widget) => widget.remove());

        const script = document.createElement('script');
        const configuration = JSON.parse(widgetConfig.textContent);

        configuration.colorTheme = theme;
        configuration.isTransparent = false;
        script.type = 'text/javascript';
        script.async = true;
        script.src = widgetConfig.dataset.src;
        script.innerHTML = JSON.stringify(configuration);
        script.dataset.generatedTradingviewWidget = 'true';

        container.appendChild(script);
        widgetConfig.dataset.loadedTheme = theme;
    });
};

const observeTradingViewTheme = () => {
    loadTradingViewWidgets();

    if (!window.__tradingViewThemeObserver) {
        window.__tradingViewThemeObserver = new MutationObserver(loadTradingViewWidgets);
        window.__tradingViewThemeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });
    }
};

document.addEventListener('DOMContentLoaded', observeTradingViewTheme);
document.addEventListener('livewire:navigated', observeTradingViewTheme);
