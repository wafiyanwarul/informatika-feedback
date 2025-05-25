<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyQuestionResource\Pages;
use App\Filament\Resources\SurveyQuestionResource\RelationManagers;
use App\Filament\Resources\SurveyQuestionResource\Pages\ListSurveyQuestions;
use App\Filament\Resources\SurveyQuestionResource\Pages\ViewSurveyQuestions;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SurveyQuestionResource extends Resource
{
    protected static ?string $model = SurveyQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manage Surveys';
    protected static ?string $navigationLabel = 'Survey Questions';
    protected static ?string $pluralModelLabel = 'Survey Questions';

    public static function shouldRegisterNavigation(): bool
    {
        return true; // to hide/show this resource in the navigation menu
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveyQuestions::route('/'),
            'create' => Pages\CreateSurveyQuestion::route('/create'),
            'edit' => Pages\EditSurveyQuestion::route('/{record}/edit'),
            'view-survey' => Pages\ViewSurveyQuestions::route('/{record}/questions'),
        ];
    }
}
