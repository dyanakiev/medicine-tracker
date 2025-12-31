import { Link, router, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { translate } from '../../lib/translate';

const scheduleSummary = (medicine, t) => {
    switch (medicine.scheduleType) {
        case 'hours':
            return t('medicines.schedule_hours', { hours: medicine.frequencyHours });
        case 'days':
            return t('medicines.schedule_days', {
                days: medicine.frequencyDays,
                time: medicine.timeOfDay ?? '--:--',
            });
        case 'weekdays':
            return t('medicines.schedule_weekdays', {
                days: medicine.weekdays.length ? medicine.weekdays.join(', ') : t('medicines.not_scheduled'),
                time: medicine.timeOfDay ?? '--:--',
            });
        case 'times':
            return t('medicines.schedule_times', {
                times: medicine.times.length ? medicine.times.join(', ') : t('medicines.not_scheduled'),
            });
        case 'dates':
            return t('medicines.schedule_dates', {
                dates: medicine.dates.length ? medicine.dates.join(', ') : t('medicines.not_scheduled'),
                time: medicine.timeOfDay ?? '--:--',
            });
        case 'as_needed':
            return t('medicines.as_needed');
        default:
            return t('medicines.not_scheduled');
    }
};

const MedicinesIndex = () => {
    const { props } = usePage();
    const {
        scheduledMedicines,
        asNeededMedicines,
        dueNowCount,
        upNextCount,
        takenTodayCount,
        compactView,
        statusFilter,
        sortOrder,
        historyOpen,
        historyLimits,
        trackedLabel,
        translations,
    } = props;

    const [filtersOpen, setFiltersOpen] = useState(false);

    const t = useMemo(() => {
        return (key, replacements) => translate(translations, key, replacements);
    }, [translations]);

    const buildQuery = (overrides = {}) => ({
        status: statusFilter,
        sort: sortOrder,
        open: historyOpen,
        history: historyLimits,
        ...overrides,
    });

    const updateFilters = (nextStatus, nextSort) => {
        router.get('/medicines', buildQuery({ status: nextStatus, sort: nextSort }), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const toggleHistory = (id) => {
        const isOpen = historyOpen.includes(id);
        const nextOpen = isOpen
            ? historyOpen.filter((openId) => openId !== id)
            : [...historyOpen, id];

        router.get('/medicines', buildQuery({ open: nextOpen }), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const loadMoreHistory = (id) => {
        const current = historyLimits[id] ?? 5;
        const nextLimits = { ...historyLimits, [id]: current + 5 };
        const nextOpen = historyOpen.includes(id) ? historyOpen : [...historyOpen, id];

        router.get('/medicines', buildQuery({ open: nextOpen, history: nextLimits }), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const markTaken = (id) => {
        router.post(`/medicines/${id}/taken`, {}, { preserveScroll: true });
    };

    const deleteMedicine = (medicine) => {
        const confirmed = window.confirm(
            t('dialogs.delete_body', { name: medicine.name }),
        );

        if (!confirmed) {
            return;
        }

        router.post(`/medicines/${medicine.id}/delete`, {}, { preserveScroll: true });
    };

    const deleteDoseLog = (doseLog) => {
        const confirmed = window.confirm(
            t('medicines.history_entry', { time: doseLog.takenAtLabel }),
        );

        if (!confirmed) {
            return;
        }

        router.post(`/dose-logs/${doseLog.id}/delete`, {}, { preserveScroll: true });
    };

    const renderHistory = (medicine) => {
        if (!historyOpen.includes(medicine.id)) {
            return null;
        }

        return (
            <div className="rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 px-3 py-2 text-xs text-slate-600 dark:text-slate-300">
                {medicine.notes && (
                    <div className="space-y-1">
                        <p className="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{t('medicines.notes_label')}</p>
                        <p>{medicine.notes}</p>
                    </div>
                )}
                <p className={`text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400 ${medicine.notes ? 'mt-3' : ''}`}>{t('medicines.history')}</p>
                <div className="mt-2 space-y-1">
                    {medicine.doseLogs.length ? medicine.doseLogs.map((doseLog) => (
                        <div key={doseLog.id} className="flex items-center justify-between gap-2">
                            <p>{t('medicines.history_entry', { time: doseLog.takenAtLabel })}</p>
                            <button
                                type="button"
                                onClick={() => deleteDoseLog(doseLog)}
                                aria-label={t('actions.remove')}
                                className="inline-flex items-center justify-center rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-200 dark:hover:bg-rose-900/50"
                            >
                                {t('actions.remove')}
                            </button>
                        </div>
                    )) : (
                        <p className="text-slate-500 dark:text-slate-400">{t('medicines.history_empty')}</p>
                    )}
                </div>
                {medicine.doseLogsCount > medicine.doseLogs.length && (
                    <button
                        type="button"
                        onClick={() => loadMoreHistory(medicine.id)}
                        className="mt-2 text-[11px] font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-slate-100"
                    >
                        {t('actions.load_more')}
                    </button>
                )}
            </div>
        );
    };

    return (
        <AppLayout>
            <div className="space-y-6">
                <header className="space-y-3 animate-fade-up">
                    <div className="inline-flex items-center gap-2 rounded-full border border-teal-200/70 bg-teal-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-700 dark:border-teal-500/40 dark:bg-teal-500/10 dark:text-teal-200">
                        <span className="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
                        {t('badges.care_plan')}
                    </div>
                    <h1 className="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{t('headers.medicines_glance')}</h1>
                    <p className="text-sm text-slate-600 dark:text-slate-400">{t('descriptions.medicine_intro')}</p>
                </header>

                <section className="animate-fade-up animation-delay-150">
                    <div className="app-card px-4 py-3">
                        <div className="flex flex-wrap items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <div className="flex items-center gap-2 rounded-full border border-rose-100/80 dark:border-rose-900/40 bg-rose-50/70 dark:bg-rose-900/20 px-3 py-1">
                                <span className="h-2 w-2 rounded-full bg-rose-500"></span>
                                <span className="text-[11px] uppercase tracking-widest text-rose-500">{t('stats.due_now')}</span>
                                <span className="text-base font-semibold text-slate-900 dark:text-white">{dueNowCount}</span>
                            </div>
                            <div className="flex items-center gap-2 rounded-full border border-sky-100/80 dark:border-sky-900/40 bg-sky-50/70 dark:bg-sky-900/20 px-3 py-1">
                                <span className="h-2 w-2 rounded-full bg-sky-500"></span>
                                <span className="text-[11px] uppercase tracking-widest text-sky-600">{t('stats.next_24h')}</span>
                                <span className="text-base font-semibold text-slate-900 dark:text-white">{upNextCount}</span>
                            </div>
                            <div className="flex items-center gap-2 rounded-full border border-emerald-100/80 dark:border-emerald-900/40 bg-emerald-50/70 dark:bg-emerald-900/20 px-3 py-1">
                                <span className="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span className="text-[11px] uppercase tracking-widest text-emerald-600">{t('stats.taken_today')}</span>
                                <span className="text-base font-semibold text-slate-900 dark:text-white">{takenTodayCount}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div className="mb-4 space-y-3">
                        <div className="flex items-center justify-between gap-3">
                            <h2 className="text-2xl font-semibold">{t('medicines.schedule')}</h2>
                            <button
                                type="button"
                                onClick={() => setFiltersOpen((open) => !open)}
                                aria-label={t('medicines.filters')}
                                className="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 text-slate-600 dark:text-slate-200 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                            >
                                <svg className="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M2 3.5H14M4.5 8H11.5M6.5 12.5H9.5" stroke="currentColor" strokeLinecap="round" strokeWidth="1.5"/>
                                    <circle cx="6" cy="3.5" r="1.5" fill="currentColor"/>
                                    <circle cx="10" cy="8" r="1.5" fill="currentColor"/>
                                    <circle cx="8" cy="12.5" r="1.5" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                        <p className="text-sm text-slate-500 dark:text-slate-400">{trackedLabel}</p>
                        {filtersOpen && (
                            <div className="app-card p-4 space-y-3">
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{t('medicines.filter_label')}</p>
                                    <div className="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-xs shadow-sm">
                                        <button
                                            type="button"
                                            onClick={() => updateFilters('active', sortOrder)}
                                            className={`px-3 py-1.5 font-semibold transition-colors ${statusFilter === 'active' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100'}`}
                                        >
                                            {t('medicines.filter_active')}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => updateFilters('paused', sortOrder)}
                                            className={`px-3 py-1.5 font-semibold transition-colors ${statusFilter === 'paused' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100'}`}
                                        >
                                            {t('medicines.filter_paused')}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => updateFilters('all', sortOrder)}
                                            className={`px-3 py-1.5 font-semibold transition-colors ${statusFilter === 'all' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100'}`}
                                        >
                                            {t('medicines.filter_all')}
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{t('medicines.sort_label')}</p>
                                    <div className="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-sm shadow-sm">
                                        <button
                                            type="button"
                                            onClick={() => updateFilters(statusFilter, 'soonest')}
                                            aria-label={t('sort.soonest')}
                                            className={`inline-flex items-center justify-center h-9 w-9 transition-colors ${sortOrder === 'soonest' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100'}`}
                                        >
                                            <svg className="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M8 2.5V13.5M8 2.5L4.5 6M8 2.5L11.5 6" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => updateFilters(statusFilter, 'latest')}
                                            aria-label={t('sort.latest')}
                                            className={`inline-flex items-center justify-center h-9 w-9 transition-colors ${sortOrder === 'latest' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100'}`}
                                        >
                                            <svg className="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M8 2.5V13.5M8 2.5L4.5 6M8 2.5L11.5 6" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>

                    {compactView ? (
                        <div className="space-y-4">
                            {scheduledMedicines.length ? scheduledMedicines.map((medicine) => (
                                <div key={medicine.id} className="app-card app-card-interactive p-4 space-y-4 min-w-0">
                                    <div className="flex items-start gap-3 min-w-0">
                                        <div className="flex-1 min-w-0">
                                            <p className="text-base font-semibold text-slate-900 dark:text-slate-100 truncate">{medicine.name}</p>
                                            <p className="text-xs text-slate-500 dark:text-slate-400">{medicine.dosage} · {scheduleSummary(medicine, t)}</p>
                                            {medicine.notes && (
                                                <button
                                                    type="button"
                                                    onClick={() => toggleHistory(medicine.id)}
                                                    className="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                                >
                                                    <span className="font-semibold text-slate-600 dark:text-slate-300">{t('medicines.notes_label')}</span>
                                                    {medicine.notes}
                                                </button>
                                            )}
                                        </div>
                                        <span className={`text-xs font-semibold ${medicine.isDue ? 'text-rose-600 dark:text-rose-300' : 'text-slate-500 dark:text-slate-400'}`}>
                                            {medicine.statusLabel}
                                        </span>
                                    </div>
                                    <div className="flex items-center justify-between gap-3">
                                        <div className="text-xs text-slate-500 dark:text-slate-400">
                                            {medicine.lastTakenAt ? t('medicines.last_taken', { time: medicine.lastTakenAt }) : t('medicines.not_taken')}
                                        </div>
                                        <div className="flex gap-2">
                                            <button
                                                type="button"
                                                onClick={() => markTaken(medicine.id)}
                                                className="inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors hover:bg-emerald-600"
                                            >
                                                <span>{t('actions.taken')}</span>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => toggleHistory(medicine.id)}
                                                aria-label={t('actions.view_history', { name: medicine.name })}
                                                title={t('actions.history')}
                                                className="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M4 4v4h4"/>
                                                </svg>
                                            </button>
                                            <Link
                                                href={`/medicines/${medicine.id}/edit`}
                                                aria-label={t('actions.edit_medicine', { name: medicine.name })}
                                                title={t('actions.edit')}
                                                className="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => deleteMedicine(medicine)}
                                                aria-label={t('actions.delete_medicine', { name: medicine.name })}
                                                title={t('actions.delete')}
                                                className="h-9 w-9 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    {renderHistory(medicine)}
                                </div>
                            )) : (
                                <div className="text-center py-12">
                                    <p className="text-slate-500 dark:text-slate-400">{t('medicines.empty')}</p>
                                </div>
                            )}
                            {asNeededMedicines.length > 0 && (
                                <div className="mt-6 space-y-3">
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{t('medicines.as_needed_section')}</h3>
                                    </div>
                                    <div className="space-y-4">
                                        {asNeededMedicines.map((medicine) => (
                                            <div key={`as-needed-${medicine.id}`} className="app-card app-card-interactive p-4 space-y-4 min-w-0">
                                                <div className="flex items-start gap-3 min-w-0">
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-base font-semibold text-slate-900 dark:text-slate-100 truncate">{medicine.name}</p>
                                                        <p className="text-xs text-slate-500 dark:text-slate-400">{medicine.dosage}</p>
                                                        {medicine.notes && (
                                                            <button
                                                                type="button"
                                                                onClick={() => toggleHistory(medicine.id)}
                                                                className="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                                            >
                                                                <span className="font-semibold text-slate-600 dark:text-slate-300">{t('medicines.notes_label')}</span>
                                                                {medicine.notes}
                                                            </button>
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between gap-3">
                                                    <div className="text-xs text-slate-500 dark:text-slate-400">
                                                        {medicine.lastTakenAt ? t('medicines.last_taken', { time: medicine.lastTakenAt }) : t('medicines.not_taken')}
                                                    </div>
                                                    <div className="flex gap-2">
                                                        <button
                                                            type="button"
                                                            onClick={() => markTaken(medicine.id)}
                                                            className="inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors hover:bg-emerald-600"
                                                        >
                                                            <span>{t('actions.taken')}</span>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => toggleHistory(medicine.id)}
                                                            aria-label={t('actions.view_history', { name: medicine.name })}
                                                            title={t('actions.history')}
                                                            className="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                                        >
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M4 4v4h4"/>
                                                            </svg>
                                                        </button>
                                                        <Link
                                                            href={`/medicines/${medicine.id}/edit`}
                                                            aria-label={t('actions.edit_medicine', { name: medicine.name })}
                                                            title={t('actions.edit')}
                                                            className="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                                        >
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </Link>
                                                        <button
                                                            type="button"
                                                            onClick={() => deleteMedicine(medicine)}
                                                            aria-label={t('actions.delete_medicine', { name: medicine.name })}
                                                            title={t('actions.delete')}
                                                            className="h-9 w-9 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                                        >
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                {renderHistory(medicine)}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {scheduledMedicines.length ? scheduledMedicines.map((medicine) => (
                                <div key={medicine.id} className="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/70 shadow-sm p-5 flex flex-col justify-between gap-6 app-card-interactive min-w-0">
                                    <div className="space-y-3">
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{t('medicines.next_dose')}</p>
                                                <p className="text-2xl font-semibold text-slate-900 dark:text-white">{medicine.statusLabel}</p>
                                            </div>
                                            <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold ${medicine.isDue ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200' : 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-200'}`}>
                                                {medicine.isActive ? t('medicines.active') : t('medicines.paused')}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-3 min-w-0">
                                            <div className="min-w-0">
                                                <p className="text-lg font-semibold text-slate-900 dark:text-slate-100">{medicine.name}</p>
                                                <p className="text-sm text-slate-500 dark:text-slate-400">{medicine.dosage} · {scheduleSummary(medicine, t)}</p>
                                                {medicine.notes && (
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleHistory(medicine.id)}
                                                        className="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        <span className="font-semibold text-slate-600 dark:text-slate-300">{t('medicines.notes_label')}</span>
                                                        {medicine.notes}
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                            {medicine.lastTakenAt ? t('medicines.last_taken', { time: medicine.lastTakenAt }) : t('medicines.not_taken')}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() => markTaken(medicine.id)}
                                            className="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 text-white px-4 py-2 text-sm font-semibold shadow-sm transition-colors hover:bg-emerald-600"
                                        >
                                            {t('actions.taken')}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => toggleHistory(medicine.id)}
                                            aria-label={t('actions.view_history', { name: medicine.name })}
                                            title={t('actions.history')}
                                            className="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M4 4v4h4"/>
                                            </svg>
                                        </button>
                                        <Link
                                            href={`/medicines/${medicine.id}/edit`}
                                            aria-label={t('actions.edit_medicine', { name: medicine.name })}
                                            title={t('actions.edit')}
                                            className="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={() => deleteMedicine(medicine)}
                                            aria-label={t('actions.delete_medicine', { name: medicine.name })}
                                            title={t('actions.delete')}
                                            className="h-10 w-10 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    {renderHistory(medicine)}
                                </div>
                            )) : (
                                <div className="col-span-full text-center py-12">
                                    <p className="text-slate-500 dark:text-slate-400">{t('medicines.empty')}</p>
                                </div>
                            )}
                            {asNeededMedicines.length > 0 && (
                                <div className="col-span-full mt-6 space-y-3">
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{t('medicines.as_needed_section')}</h3>
                                    </div>
                                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {asNeededMedicines.map((medicine) => (
                                            <div key={`as-needed-${medicine.id}`} className="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/70 shadow-sm p-5 flex flex-col justify-between gap-6 app-card-interactive min-w-0">
                                                <div className="space-y-3">
                                                    <div className="flex items-center gap-3 min-w-0">
                                                        <div className="min-w-0">
                                                            <p className="text-lg font-semibold text-slate-900 dark:text-slate-100">{medicine.name}</p>
                                                            <p className="text-sm text-slate-500 dark:text-slate-400">{medicine.dosage}</p>
                                                            {medicine.notes && (
                                                                <button
                                                                    type="button"
                                                                    onClick={() => toggleHistory(medicine.id)}
                                                                    className="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                                                >
                                                                    <span className="font-semibold text-slate-600 dark:text-slate-300">{t('medicines.notes_label')}</span>
                                                                    {medicine.notes}
                                                                </button>
                                                            )}
                                                        </div>
                                                    </div>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                                        {medicine.lastTakenAt ? t('medicines.last_taken', { time: medicine.lastTakenAt }) : t('medicines.not_taken')}
                                                    </p>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => markTaken(medicine.id)}
                                                        className="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 text-white px-4 py-2 text-sm font-semibold shadow-sm transition-colors hover:bg-emerald-600"
                                                    >
                                                        {t('actions.taken')}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleHistory(medicine.id)}
                                                        aria-label={t('actions.view_history', { name: medicine.name })}
                                                        title={t('actions.history')}
                                                        className="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.6" d="M4 4v4h4"/>
                                                        </svg>
                                                    </button>
                                                    <Link
                                                        href={`/medicines/${medicine.id}/edit`}
                                                        aria-label={t('actions.edit_medicine', { name: medicine.name })}
                                                        title={t('actions.edit')}
                                                        className="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() => deleteMedicine(medicine)}
                                                        aria-label={t('actions.delete_medicine', { name: medicine.name })}
                                                        title={t('actions.delete')}
                                                        className="h-10 w-10 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                {renderHistory(medicine)}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </section>
            </div>
        </AppLayout>
    );
};

export default MedicinesIndex;
