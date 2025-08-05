import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function UserForm({
                                     auth,
                                     user = null,
                                     regions = [],
                                     isEdit = false,
                                     roles

                                 }) {
    const [formData, setFormData] = useState({
        name: user?.name || '',
        email: user?.email || '',
        password: '',
        password_confirmation: '',
        region_id: user?.region_id || '',
        role: user?.role || 'translator',
        is_active: user?.is_active ?? true,
    });

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (isEdit && user?.id) {
            router.put(`/users/${user.id}`, formData, {
                onError: (errors) => {
                    console.error('Ошибка при обновлении:', errors);
                },
                onSuccess: () => {
                    // Действия после успешного обновления
                }
            });
        } else {
            router.post('/users', formData, {
                onError: (errors) => {
                    console.error('Ошибка при создании:', errors);
                },
                onSuccess: () => {
                    // Действия после успешного создания
                }
            });
        }
    };

    const currentUserRole = auth?.user?.role || '';
    const isFadn = currentUserRole === 'fadn';
    const isRegionAdmin = currentUserRole === 'region_admin';

    return (
        <AuthenticatedLayout auth={auth}>
            <Head title={isEdit ? 'Редактирование пользователя' : 'Создание пользователя'} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h2 className="text-2xl font-bold mb-6">
                                {isEdit ? 'Редактирование пользователя' : 'Создание пользователя'}
                            </h2>

                            <form onSubmit={handleSubmit}>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                                        <input
                                            type="text"
                                            name="name"
                                            value={formData.name}
                                            onChange={handleChange}
                                            required
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleChange}
                                            required
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                                        <input
                                            type="password"
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            required={!isEdit}
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Подтверждение пароля</label>
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            value={formData.password_confirmation}
                                            onChange={handleChange}
                                            required={!isEdit}
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Регион</label>
                                        <select
                                            name="region_id"
                                            value={formData.region_id}
                                            onChange={handleChange}
                                            required
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            disabled={!isFadn && isEdit}
                                        >
                                            <option value="">Выберите регион</option>
                                            {regions.map(region => (
                                                <option
                                                    key={region.id}
                                                    value={region.id}
                                                    disabled={isRegionAdmin && region.id !== auth.user.region_id}
                                                >
                                                    {region.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                                        <select
                                            name="role"
                                            value={formData.role}
                                            onChange={handleChange}
                                            required
                                            className="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            disabled={
                                                (!isFadn &&
                                                    (isEdit ? user?.role === 'region_admin' : formData.role === 'region_admin')) ||
                                                (isRegionAdmin &&
                                                    ['fadn', 'region_admin'].includes(formData.role))
                                            }
                                        >
                                            {Object.entries(roles).map(([value, label]) => (
                                                <option
                                                    key={value}
                                                    value={value}
                                                    disabled={
                                                        (isRegionAdmin &&
                                                            ['fadn', 'region_admin'].includes(value)) ||
                                                        (!isFadn && value === 'region_admin')
                                                    }
                                                >
                                                    {label}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            checked={formData.is_active}
                                            onChange={handleChange}
                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label className="ml-2 block text-sm text-gray-700">Активный</label>
                                    </div>
                                </div>

                                <div className="flex justify-end space-x-4">
                                    <Link
                                        href="/users"
                                        className="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    >
                                        Отмена
                                    </Link>
                                    <button
                                        type="submit"
                                        className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        {isEdit ? 'Обновить' : 'Создать'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
