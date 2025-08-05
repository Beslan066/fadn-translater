import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TranslationsForm from '@/Components/TranslationsForm.jsx';
import TranslationsList from '@/Components/TranslationsList';

export default function TranslatePage({ auth }) {
    const [sentence, setSentence] = useState(null);
    const [translations, setTranslations] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    const fetchRandomSentence = () => {
        setIsLoading(true);
        Inertia.get('/sentences/random', {}, {
            preserveState: true,
            onSuccess: (page) => {
                setSentence(page.props.sentence);
                setIsLoading(false);
            }
        });
    };

    const fetchTranslations = () => {
        Inertia.get('/translations', {}, {
            preserveState: true,
            onSuccess: (page) => {
                setTranslations(page.props.translations);
            }
        });
    };

    useEffect(() => {
        fetchRandomSentence();
        fetchTranslations();
    }, []);

    const handleSubmit = (translationText) => {
        Inertia.post('/translations', {
            sentence_id: sentence.id,
            translation: translationText
        }, {
            onSuccess: () => {
                fetchRandomSentence();
                fetchTranslations();
            }
        });
    };

    return (
        <AuthenticatedLayout auth={auth}>
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h2 className="text-2xl font-bold mb-6">Новое предложение для перевода</h2>
                            {isLoading ? (
                                <p>Загрузка...</p>
                            ) : sentence ? (
                                <TranslationsForm
                                    sentence={sentence}
                                    onSubmit={handleSubmit}
                                    onSkip={fetchRandomSentence}
                                />
                            ) : (
                                <p>Нет доступных предложений для перевода</p>
                            )}
                        </div>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h2 className="text-2xl font-bold mb-6">Мои переводы</h2>
                            <TranslationsList translations={translations} />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
