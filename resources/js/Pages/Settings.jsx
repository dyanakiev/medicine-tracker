import { router, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import AppLayout from '../Layouts/AppLayout';
import { translate } from '../lib/translate';

const Settings = () => {
    const { props } = usePage();
    const { compactView, languages, timezones, locale, timezone, translations } = props;

    const t = useMemo(() => {
        return (key, replacements) => translate(translations, key, replacements);
    }, [translations]);

    const updateSetting = (payload) => {
        router.post('/settings', { _method: 'put', ...payload }, {
            preserveScroll: true,
            headers: {
                'X-HTTP-Method-Override': 'PUT',
            },
        });
    };

    return (
        <AppLayout>
            <div className="space-y-8">
                <header className="space-y-4 animate-fade-up">
                    <div className="inline-flex items-center gap-2 rounded-full border border-sky-200/70 bg-sky-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200">
                        <span className="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                        {t('badges.preferences')}
                    </div>
                    <h1 className="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{t('headers.personalize')}</h1>
                    <p className="text-slate-600 dark:text-slate-400 mt-2">{t('descriptions.manage_preferences')}</p>
                </header>

                <div className="app-card-strong p-6 animate-fade-up animation-delay-150">
                    <h3 className="text-xl font-semibold mb-6">{t('settings.display')}</h3>

                    <div className="space-y-4">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{t('settings.compact')}</p>
                                <p className="text-sm text-slate-500 dark:text-slate-400">{t('settings.compact_help')}</p>
                            </div>
                            <button
                                type="button"
                                onClick={() => updateSetting({ compact_view: !compactView })}
                                className="inline-flex items-center gap-2 rounded-full border border-slate-200/80 dark:border-slate-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 bg-white dark:bg-slate-900/50 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                            >
                                <span className={`h-2 w-2 rounded-full ${compactView ? 'bg-emerald-500' : 'bg-slate-400'}`}></span>
                                {compactView ? t('settings.enabled') : t('settings.disabled')}
                            </button>
                        </div>
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{t('settings.language')}</p>
                                <p className="text-sm text-slate-500 dark:text-slate-400">{t('settings.language_help')}</p>
                            </div>
                            <select
                                value={locale}
                                onChange={(event) => updateSetting({ locale: event.target.value })}
                                className="w-full sm:w-52 rounded-full border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/50 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                            >
                                {Object.entries(languages).map(([value, label]) => (
                                    <option key={value} value={value}>
                                        {label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{t('settings.timezone')}</p>
                                <p className="text-sm text-slate-500 dark:text-slate-400">{t('settings.timezone_help')}</p>
                            </div>
                            <select
                                value={timezone}
                                onChange={(event) => updateSetting({ timezone: event.target.value })}
                                className="w-full sm:w-72 rounded-full border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/50 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                            >
                                {timezones.map((zone) => (
                                    <option key={zone} value={zone}>
                                        {zone}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default Settings;
