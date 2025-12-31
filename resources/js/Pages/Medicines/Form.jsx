import { Link, useForm, usePage } from '@inertiajs/react';
import { useEffect, useMemo } from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { translate } from '../../lib/translate';

const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const MedicineForm = () => {
    const { props } = usePage();
    const { medicine, translations } = props;
    const isEditing = Boolean(medicine);

    const t = useMemo(() => {
        return (key, replacements) => translate(translations, key, replacements);
    }, [translations]);

    const form = useForm(
        medicine
            ? {
                name: medicine.name ?? '',
                dosage: medicine.dosage ?? '',
                schedule_type: medicine.scheduleType ?? 'hours',
                frequency_hours: medicine.frequencyHours ?? '',
                frequency_days: medicine.frequencyDays ?? '',
                weekdays: medicine.weekdays ?? [],
                time_of_day: medicine.timeOfDay ?? '',
                times_input: medicine.timesInput ?? '',
                dates_input: medicine.datesInput ?? '',
                notes: medicine.notes ?? '',
                next_dose_at: medicine.nextDoseAt ?? '',
                is_active: medicine.isActive ?? true,
            }
            : {
                name: '',
                dosage: '',
                schedule_type: 'hours',
                frequency_hours: '',
                frequency_days: '',
                weekdays: [],
                time_of_day: '',
                times_input: '',
                dates_input: '',
                notes: '',
                next_dose_at: '',
            },
    );

    useEffect(() => {
        const defaults = medicine
            ? {
                name: medicine.name ?? '',
                dosage: medicine.dosage ?? '',
                schedule_type: medicine.scheduleType ?? 'hours',
                frequency_hours: medicine.frequencyHours ?? '',
                frequency_days: medicine.frequencyDays ?? '',
                weekdays: medicine.weekdays ?? [],
                time_of_day: medicine.timeOfDay ?? '',
                times_input: medicine.timesInput ?? '',
                dates_input: medicine.datesInput ?? '',
                notes: medicine.notes ?? '',
                next_dose_at: medicine.nextDoseAt ?? '',
                is_active: medicine.isActive ?? true,
            }
            : {
                name: '',
                dosage: '',
                schedule_type: 'hours',
                frequency_hours: '',
                frequency_days: '',
                weekdays: [],
                time_of_day: '',
                times_input: '',
                dates_input: '',
                notes: '',
                next_dose_at: '',
            };

        form.setDefaults(defaults);
        form.reset();
        form.clearErrors();
    }, [medicine?.id]);

    const submit = (event) => {
        event.preventDefault();

        if (isEditing) {
            form.post(`/medicines/${medicine.id}/update`, {
                preserveScroll: true
            });
        } else {
            form.post('/medicines', {
                preserveScroll: true,
            });
        }
    };

    const scheduleType = form.data.schedule_type;

    return (
        <AppLayout>
            <div className="space-y-8">
                <header className="space-y-4 animate-fade-up">
                    <div className="inline-flex items-center gap-2 rounded-full border border-cyan-200/70 bg-cyan-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-700 dark:border-cyan-500/40 dark:bg-cyan-500/10 dark:text-cyan-200">
                        <span className="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                        {isEditing ? t('badges.update') : t('badges.create')}
                    </div>
                    <h1 className="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
                        {isEditing ? t('titles.edit_medicine') : t('titles.add_medicine')}
                    </h1>
                    <p className="text-slate-600 dark:text-slate-400">
                        {isEditing ? t('descriptions.medicine_edit') : t('descriptions.medicine_add')}
                    </p>
                </header>

                <div className="app-card-strong p-6 animate-fade-up animation-delay-150">
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {t('form.name')}
                            </label>
                            <input
                                type="text"
                                id="name"
                                value={form.data.name}
                                onChange={(event) => form.setData('name', event.target.value)}
                                className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder={t('form.name_placeholder')}
                            />
                            {form.errors.name && (
                                <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.name}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="dosage" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {t('form.dosage')}
                            </label>
                            <input
                                type="text"
                                id="dosage"
                                value={form.data.dosage}
                                onChange={(event) => form.setData('dosage', event.target.value)}
                                className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder={t('form.dosage_placeholder')}
                            />
                            {form.errors.dosage && (
                                <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.dosage}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="scheduleType" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {t('form.schedule_type')}
                            </label>
                            <select
                                id="scheduleType"
                                value={form.data.schedule_type}
                                onChange={(event) => form.setData('schedule_type', event.target.value)}
                                className="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            >
                                <option value="hours">{t('form.schedule_hours')}</option>
                                <option value="days">{t('form.schedule_days')}</option>
                                <option value="weekdays">{t('form.schedule_weekdays')}</option>
                                <option value="times">{t('form.schedule_times')}</option>
                                <option value="dates">{t('form.schedule_dates')}</option>
                                <option value="as_needed">{t('form.schedule_as_needed')}</option>
                            </select>
                            {form.errors.schedule_type && (
                                <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.schedule_type}</p>
                            )}
                        </div>

                        {scheduleType === 'as_needed' && (
                            <div className="rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/50 px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                                {t('form.as_needed_help')}
                            </div>
                        )}

                        {scheduleType === 'hours' && (
                            <div>
                                <label htmlFor="frequencyHours" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    {t('form.frequency_hours')}
                                </label>
                                <input
                                    type="number"
                                    id="frequencyHours"
                                    value={form.data.frequency_hours}
                                    onChange={(event) => form.setData('frequency_hours', event.target.value)}
                                    min="1"
                                    max="168"
                                    className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    placeholder={t('form.frequency_hours_placeholder')}
                                />
                                {form.errors.frequency_hours && (
                                    <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.frequency_hours}</p>
                                )}
                            </div>
                        )}

                        {scheduleType === 'days' && (
                            <div className="space-y-3">
                                <div>
                                    <label htmlFor="frequencyDays" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {t('form.frequency_days')}
                                    </label>
                                    <input
                                        type="number"
                                        id="frequencyDays"
                                        value={form.data.frequency_days}
                                        onChange={(event) => form.setData('frequency_days', event.target.value)}
                                        min="1"
                                        max="365"
                                        className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                        placeholder={t('form.frequency_days_placeholder')}
                                    />
                                    {form.errors.frequency_days && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.frequency_days}</p>
                                    )}
                                </div>
                                <div>
                                    <label htmlFor="timeOfDayDays" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {t('form.time_of_day')}
                                    </label>
                                    <input
                                        type="time"
                                        id="timeOfDayDays"
                                        value={form.data.time_of_day}
                                        onChange={(event) => form.setData('time_of_day', event.target.value)}
                                        className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    />
                                    {form.errors.time_of_day && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.time_of_day}</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {scheduleType === 'weekdays' && (
                            <div className="space-y-3">
                                <div>
                                    <p className="text-sm font-medium text-slate-700 dark:text-slate-300">{t('form.weekdays')}</p>
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {WEEKDAYS.map((day) => (
                                            <label key={day} className="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input
                                                    type="checkbox"
                                                    value={day}
                                                    checked={form.data.weekdays.includes(day)}
                                                    onChange={(event) => {
                                                        const next = event.target.checked
                                                            ? [...form.data.weekdays, day]
                                                            : form.data.weekdays.filter((item) => item !== day);
                                                        form.setData('weekdays', next);
                                                    }}
                                                    className="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
                                                />
                                                {t(`form.weekday_${day}`)}
                                            </label>
                                        ))}
                                    </div>
                                    {form.errors.weekdays && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.weekdays}</p>
                                    )}
                                </div>
                                <div>
                                    <label htmlFor="timeOfDayWeekdays" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {t('form.time_of_day')}
                                    </label>
                                    <input
                                        type="time"
                                        id="timeOfDayWeekdays"
                                        value={form.data.time_of_day}
                                        onChange={(event) => form.setData('time_of_day', event.target.value)}
                                        className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    />
                                    {form.errors.time_of_day && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.time_of_day}</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {scheduleType === 'times' && (
                            <div>
                                <label htmlFor="timesInput" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    {t('form.times')}
                                </label>
                                <input
                                    type="text"
                                    id="timesInput"
                                    value={form.data.times_input}
                                    onChange={(event) => form.setData('times_input', event.target.value)}
                                    className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    placeholder={t('form.times_placeholder')}
                                />
                                <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{t('form.times_help')}</p>
                                {form.errors.times_input && (
                                    <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.times_input}</p>
                                )}
                            </div>
                        )}

                        {scheduleType === 'dates' && (
                            <div className="space-y-3">
                                <div>
                                    <label htmlFor="datesInput" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {t('form.dates')}
                                    </label>
                                    <input
                                        type="text"
                                        id="datesInput"
                                        value={form.data.dates_input}
                                        onChange={(event) => form.setData('dates_input', event.target.value)}
                                        className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                        placeholder={t('form.dates_placeholder')}
                                    />
                                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{t('form.dates_help')}</p>
                                    {form.errors.dates_input && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.dates_input}</p>
                                    )}
                                </div>
                                <div>
                                    <label htmlFor="timeOfDayDates" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {t('form.time_of_day')}
                                    </label>
                                    <input
                                        type="time"
                                        id="timeOfDayDates"
                                        value={form.data.time_of_day}
                                        onChange={(event) => form.setData('time_of_day', event.target.value)}
                                        className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    />
                                    {form.errors.time_of_day && (
                                        <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.time_of_day}</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {scheduleType !== 'as_needed' && (
                            <div>
                                <label htmlFor="nextDoseAt" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    {t('form.next_dose')}
                                </label>
                                <input
                                    type="datetime-local"
                                    id="nextDoseAt"
                                    value={form.data.next_dose_at}
                                    onChange={(event) => form.setData('next_dose_at', event.target.value)}
                                    className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                />
                                <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{t('form.next_dose_help')}</p>
                                {form.errors.next_dose_at && (
                                    <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.next_dose_at}</p>
                                )}
                            </div>
                        )}

                        <div>
                            <label htmlFor="notes" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {t('form.notes')}
                            </label>
                            <textarea
                                id="notes"
                                rows="3"
                                value={form.data.notes}
                                onChange={(event) => form.setData('notes', event.target.value)}
                                className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder={t('form.notes_placeholder')}
                            ></textarea>
                            {form.errors.notes && (
                                <p className="mt-1 text-sm text-rose-600 dark:text-rose-400">{form.errors.notes}</p>
                            )}
                        </div>

                        {isEditing && (
                            <div className="flex items-center justify-between rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/50 px-4 py-3">
                                <div>
                                    <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{t('form.active')}</p>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">{t('form.active_help')}</p>
                                </div>
                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={Boolean(form.data.is_active)}
                                        onChange={(event) => form.setData('is_active', event.target.checked)}
                                        className="sr-only peer"
                                    />
                                    <div className="h-6 w-11 rounded-full bg-slate-200 peer-checked:bg-emerald-500 dark:bg-slate-700 transition-colors"></div>
                                    <div className="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-5"></div>
                                </label>
                            </div>
                        )}

                        <button
                            type="submit"
                            className="w-full px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-xl transition-colors shadow-sm"
                            disabled={form.processing}
                        >
                            {isEditing ? t('form.submit_update') : t('form.submit_add')}
                        </button>

                        {isEditing && (
                            <Link
                                href="/medicines"
                                className="w-full px-4 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-100 font-medium rounded-xl transition-colors inline-flex items-center justify-center"
                            >
                                {t('form.cancel')}
                            </Link>
                        )}
                    </form>
                </div>
            </div>
        </AppLayout>
    );
};

export default MedicineForm;
