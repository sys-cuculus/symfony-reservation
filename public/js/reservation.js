document.addEventListener('DOMContentLoaded', () => {
    const openingHoursElement = document.getElementById('reservation-opening-hours');
    const dateField = document.getElementById('reservation_dateAndTime_date');
    const hourField = document.getElementById('reservation_dateAndTime_time_hour');
    const minuteField = document.getElementById('reservation_dateAndTime_time_minute');

    if (!openingHoursElement || !dateField || !hourField || !minuteField) {
        return;
    }

    const openingHours = JSON.parse(openingHoursElement.textContent || '[]');
    const originalHourOptions = Array.from(hourField.options).map((option) => ({
        value: option.value,
        text: option.text,
    }));
    const originalMinuteOptions = Array.from(minuteField.options).map((option) => ({
        value: option.value,
        text: option.text,
    }));

    const toIsoDayOfWeek = (dateValue) => {
        const date = new Date(`${dateValue}T00:00:00`);

        if (Number.isNaN(date.getTime())) {
            return null;
        }

        const day = date.getDay();

        return day === 0 ? 7 : day;
    };

    const toMinutes = (time) => {
        const [hour, minute] = time.split(':').map(Number);

        return hour * 60 + minute;
    };

    const formatTimePart = (value) => String(value).padStart(2, '0');

    const buildSlots = (dayOfWeek) => {
        const slots = [];
        const dayOpeningHours = openingHours.filter((openingHour) => (
            openingHour.dayOfWeek === dayOfWeek
            && !openingHour.closed
            && openingHour.openTime
            && openingHour.closeTime
        ));

        dayOpeningHours.forEach((openingHour) => {
            const openTime = toMinutes(openingHour.openTime);
            const closeTime = toMinutes(openingHour.closeTime);

            for (let time = openTime; time < closeTime; time += 30) {
                slots.push({
                    hour: formatTimePart(Math.floor(time / 60)),
                    minute: formatTimePart(time % 60),
                });
            }
        });

        return slots;
    };

    const replaceOptions = (select, options) => {
        const currentValue = select.value;

        select.replaceChildren();

        options.forEach((option) => {
            select.add(new Option(option.text, option.value));
        });

        if (options.some((option) => option.value === currentValue)) {
            select.value = currentValue;
        }
    };

    const restoreOriginalOptions = () => {
        replaceOptions(hourField, originalHourOptions);
        replaceOptions(minuteField, originalMinuteOptions);
        hourField.disabled = false;
        minuteField.disabled = false;
    };

    const updateMinuteOptions = (slots) => {
        const selectedHour = Number(hourField.value).toString();
        const minuteOptions = slots
            .filter((slot) => Number(slot.hour).toString() === selectedHour)
            .map((slot) => ({
                value: Number(slot.minute).toString(),
                text: slot.minute,
            }));

        replaceOptions(minuteField, minuteOptions);
        minuteField.disabled = minuteOptions.length === 0;
    };

    const updateTimeOptions = () => {
        const dayOfWeek = toIsoDayOfWeek(dateField.value);

        if (dayOfWeek === null) {
            restoreOriginalOptions();
            return;
        }

        const slots = buildSlots(dayOfWeek);
        const hours = [...new Set(slots.map((slot) => slot.hour))];
        const hourOptions = hours.map((hour) => ({
            value: Number(hour).toString(),
            text: hour,
        }));

        replaceOptions(hourField, hourOptions);
        hourField.disabled = hourOptions.length === 0;

        updateMinuteOptions(slots);
    };

    dateField.addEventListener('change', updateTimeOptions);
    hourField.addEventListener('change', () => {
        const dayOfWeek = toIsoDayOfWeek(dateField.value);

        if (dayOfWeek === null) {
            return;
        }

        updateMinuteOptions(buildSlots(dayOfWeek));
    });

    updateTimeOptions();
});
