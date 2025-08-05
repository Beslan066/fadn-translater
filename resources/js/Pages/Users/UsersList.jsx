import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function UsersList({ auth, users: initialUsers, regions, filters = {} }) {
    // Initialize state with default values if filters is undefined
    const [users, setUsers] = useState(initialUsers.data);
    const [searchParams, setSearchParams] = useState({
        region_id: filters?.region_id || '',
        role: filters?.role || '',
    });

    useEffect(() => {
        const timer = setTimeout(() => {
            Inertia.get('/users', searchParams, {
                preserveState: true,
                only: ['users', 'filters'],
                replace: true,
            });
        }, 300);

        return () => clearTimeout(timer);
    }, [searchParams]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setSearchParams(prev => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleDelete = (userId) => {
        if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
            Inertia.delete(`/users/${userId}`, {
                onSuccess: () => {
                    setUsers(users.filter(user => user.id !== userId));
                }
            });
        }
    };

    return (
        <AuthenticatedLayout auth={auth}>
            <Head title="Пользователи" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Управление пользователями</h2>
                                <Link
                                    href="/users/create"
                                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Создать пользователя
                                </Link>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Регион</label>
                                    <select
                                        name="region_id"
                                        value={searchParams.region_id}
                                        onChange={handleChange}
                                        className="w-full border-gray-300 rounded-md shadow-sm"
                                    >
                                        <option value="">Все регионы</option>
                                        {regions && regions.map(region => (
                                            <option key={region.id} value={region.id}>
                                                {region.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                                    <select
                                        name="role"
                                        value={searchParams.role}
                                        onChange={handleChange}
                                        className="w-full border-gray-300 rounded-md shadow-sm"
                                    >
                                        <option value="">Все роли</option>
                                        {auth.user.roles && Object.entries(auth.user.roles).map(([value, label]) => (
                                            <option key={value} value={value}>{label}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Имя</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Регион</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Роль</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                    {users.map(user => (
                                        <tr key={user.id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">{user.name}</div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-500">{user.email}</div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-500">{user.region?.name || '-'}</div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {user.role_name}
                                                    </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                        user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {user.is_active ? 'Активен' : 'Неактивен'}
                                                    </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Link
                                                    href={`/users/${user.id}/edit`}
                                                    className="text-blue-600 hover:text-blue-900 mr-4"
                                                >
                                                    Редактировать
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(user.id)}
                                                    className="text-red-600 hover:text-red-900"
                                                    disabled={user.id === auth.user.id}
                                                >
                                                    Удалить
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
